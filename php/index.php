<?php

use com\realobjects\pdfreactor\webservice\client\Conformance;
use com\realobjects\pdfreactor\webservice\client\MediaFeature;
use com\realobjects\pdfreactor\webservice\client\PDFreactor;
use com\realobjects\pdfreactor\webservice\client\ViewerPreferences;

require_once __DIR__ . '/../PDFreactor.class.php';

$html_external_url = getenv('HTML_EXTERNAL_ENDPOINT');
$html_internal_url = getenv('HTML_INTERNAL_ENDPOINT');
$pdf_reactor_endpoint = getenv('PDF_REACTOR_ENDPOINT');
$data = json_decode(file_get_contents("$html_internal_url/list.json"), true);

function render_links(string $lang, array $item): void {
    if (!array_key_exists($lang, $item)) {
        return;
    }

    global $html_external_url;
    global $html_internal_url;
    global $pdf_reactor_endpoint;

    $params = [
        'margin' => '0.5in',
        'size' => 'letter',
        '-ro-scale-content' => '70%',
    ];
    if (!empty($item['index']['frontmatter']['page_styles'])) {
        foreach ($item['index']['frontmatter']['page_styles'] as $style => $value) {
            $params[$style] = $value;
        }
    }
    if (!empty($item[$lang]['frontmatter']['page_styles'])) {
        foreach ($item[$lang]['frontmatter']['page_styles'] as $style => $value) {
            $params[$style] = $value;
        }
    }
    ?>
        <a href="<?= $html_external_url . $item[$lang]['url'] ?>">
            HTML
        </a> |
        <a href="/?<?= http_build_query(['form' => $html_internal_url . $item[$lang]['url'], 'params' => $params]) ?>">
            PDF
        </a>
    <?php
}

if (empty($_GET['form'])) {
    ?>
    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>HCPSS Forms</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        </head>
        <body>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Form ID</th>
                    <th scope="col">Version</th>
                    <th scope="col">Title</th>
                    <th scope="col">English</th>
                    <th scope="col">Spanish</th>
                    <th scope="col">Korean</th>
                    <th scope="col">Chinese</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $form_id => $item): ?>
                    <tr>
                        <td><?= $form_id ?></td>
                        <td><?= $item['index']['frontmatter']['form_version'] ?></td>
                        <td><?= $item['en']['frontmatter']['title'] ?></td>
                        <td><?= render_links('en', $item) ?></td>
                        <td><?= render_links('es', $item) ?></td>
                        <td><?= render_links('ko', $item) ?></td>
                        <td><?= render_links('zh', $item) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </body>
    </html>
    <?php
    die;
}

$form = $_GET['form'];
$parms = $_GET['params'];
$pdf_reactor = new PDFreactor($pdf_reactor_endpoint);
$result = $pdf_reactor->convertAsBinary([
    'addTags' => TRUE,
    'conformance' => Conformance::PDFUA1,
    'document' => $form,
    'mediaFeatureValues' => [
        [
            'mediaFeature' => MediaFeature::DEVICE_WIDTH,
            'value' => '1024px',
        ], [
            'mediaFeature' => MediaFeature::WIDTH,
            'value' => '1024px',
        ],
    ],
    'viewerPreferences' => [
        ViewerPreferences::FIT_WINDOW,
    ],
    "userStyleSheets" => [
        [
            'content' => '
                @page {' .
                    array_reduce(array_keys($parms), function ($carry, $key) use ($parms) {
                        return $carry . "$key: {$parms[$key]};";
                    }, '')
                    . '}
                form, form input, form select, form textarea {
                  -ro-pdf-format: pdf;
                }
                .grid, .grid-cell, .field-wrapper, .callout, [data-content-id],
                body > div, .page-title, form, section, .stripes, .input-wrapper {
                  -ro-pdf-tag-type: none;
                }
                .page-header, main, footer {
                    -ro-pdf-tag-type: none;
                }
                .subtitle {
                    -ro-pdf-tag-type: p;
                }
            ',
        ],
    ]
]);
$filename = trim(parse_url($form, PHP_URL_PATH), '/');
header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
echo $result;
