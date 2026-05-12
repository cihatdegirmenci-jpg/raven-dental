<?php
/**
 * Local OpenCart Modification Refresh
 * docker exec raven-apache php /usr/local/bin/refresh-mods.php
 */

$root = '/var/www/html';
$modDir = '/var/www/storage/modification/';

function expandBraces($s) {
    if (strpos($s, '{') === false) return [$s];
    if (preg_match('/^(.*?)\{([^{}]+)\}(.*)$/', $s, $m)) {
        $out = [];
        foreach (explode(',', $m[2]) as $opt) {
            foreach (expandBraces($m[1].$opt.$m[3]) as $e) $out[] = $e;
        }
        return $out;
    }
    return [$s];
}

function applyOps($fileNode, $targetPath, $modDir, $root) {
    $applied = 0;
    $rel = substr($targetPath, strlen($root) + 1);
    $modPath = $modDir . $rel;

    // Start from existing modified version if any, else original
    $content = is_file($modPath) ? file_get_contents($modPath) : file_get_contents($targetPath);

    $ops = $fileNode->getElementsByTagName('operation');
    foreach ($ops as $op) {
        $searchNodes = $op->getElementsByTagName('search');
        $addNodes = $op->getElementsByTagName('add');
        if ($searchNodes->length === 0 || $addNodes->length === 0) continue;
        $searchNode = $searchNodes->item(0);
        $addNode = $addNodes->item(0);
        $search = $searchNode->nodeValue;
        $add = $addNode->nodeValue;
        $position = $addNode->getAttribute('position') ?: 'replace';
        $isRegex = ($searchNode->getAttribute('regex') === 'true');
        $errorAction = $op->getAttribute('error') ?: 'log'; // log / abort / skip

        $found = false;
        if ($isRegex) {
            $new = ($position === 'replace') ? @preg_replace($search, $add, $content)
                : (($position === 'before') ? @preg_replace($search, $add."\$0", $content)
                : @preg_replace($search, "\$0".$add, $content));
            if ($new !== null && $new !== $content) {
                $content = $new;
                $found = true;
            }
        } else {
            if (strpos($content, $search) !== false) {
                $content = ($position === 'replace') ? str_replace($search, $add, $content)
                    : (($position === 'before') ? str_replace($search, $add.$search, $content)
                    : str_replace($search, $search.$add, $content));
                $found = true;
            }
        }
        if ($found) $applied++;
    }

    if ($applied > 0) {
        @mkdir(dirname($modPath), 0755, true);
        file_put_contents($modPath, $content);
    }
    return $applied;
}

// XML sources
$sources = [
    $root . '/system/modification.xml',
    $root . '/system/journal3.ocmod.xml',
];

// Tüm dosyaları sil — clean refresh
echo "Storage modification temizleniyor...\n";
if (is_dir($modDir)) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modDir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($rii as $f) {
        if ($f->isFile()) @unlink($f->getRealPath());
        elseif ($f->isDir()) @rmdir($f->getRealPath());
    }
}

$totalApplied = 0;
$totalOps = 0;
$totalFiles = 0;

foreach ($sources as $xmlPath) {
    if (!is_file($xmlPath)) { echo "SKIP (missing): $xmlPath\n"; continue; }
    echo "İşleniyor: $xmlPath\n";

    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = true;
    if (!@$dom->loadXml(file_get_contents($xmlPath))) {
        foreach (libxml_get_errors() as $e) echo "  XML hata: ".$e->message;
        libxml_clear_errors();
        continue;
    }

    $fileNodes = $dom->getElementsByTagName('file');
    foreach ($fileNodes as $fileNode) {
        $pathAttr = $fileNode->getAttribute('path');
        $paths = explode(',', preg_replace('/\s+/', '', $pathAttr));
        foreach ($paths as $pathPat) {
            if (empty($pathPat)) continue;
            foreach (expandBraces($pathPat) as $p) {
                $glob = glob($root . '/' . $p, GLOB_BRACE);
                foreach ($glob as $tp) {
                    if (!is_file($tp)) continue;
                    $n = applyOps($fileNode, $tp, $modDir, $root);
                    if ($n > 0) {
                        $totalApplied += $n;
                        $totalFiles++;
                    }
                    $totalOps++;
                }
            }
        }
    }
}

echo "\nÖzet:\n";
echo "  Toplam operation denemesi: $totalOps\n";
echo "  Uygulanan operation: $totalApplied\n";
echo "  Modified file sayısı: $totalFiles\n";
echo "\nStorage modification içeriği:\n";
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($modDir, RecursiveDirectoryIterator::SKIP_DOTS));
$count = 0;
foreach ($rii as $f) {
    if ($f->isFile()) $count++;
}
echo "  $count dosya storage/modification altında\n";
