<?php

use com\realobjects\pdfreactor\webservice\client\Conformance;
use com\realobjects\pdfreactor\webservice\client\MediaFeature;
use com\realobjects\pdfreactor\webservice\client\PDFreactor;
use com\realobjects\pdfreactor\webservice\client\ViewerPreferences;

require_once __DIR__ . '/../PDFreactor.class.php';

$html_external_url = getenv('HTML_EXTERNAL_ENDPOINT');
$html_internal_url = getenv('HTML_INTERNAL_ENDPOINT');
$pdf_reactor_endpoint = getenv('PDF_REACTOR_ENDPOINT');

if (empty($_GET['form'])) {
    $data = json_decode(file_get_contents("$html_internal_url/list.json"), true);
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
                    <th scope="col">Links</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $item): ?>
                    <tr>
                        <td><?= $item['frontmatter']['form_id'] ?></td>
                        <td><?= $item['frontmatter']['form_version'] ?></td>
                        <td><?= $item['frontmatter']['title'] ?></td>
                        <td>
                            <a href="<?= $html_external_url . $item['url'] ?>">
                                HTML
                            </a> |
                            <a href="/?form=<?= urlencode($html_internal_url . $item['url']) ?>">
                                PDF
                            </a>
                        </td>
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
                @page {
                  margin: 0.5in;
                  size: Letter;
                  -ro-scale-content: 70%;
                }
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
