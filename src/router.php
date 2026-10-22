<?php
include 'api.php';
include __DIR__.'/../config.php';

class Router {

    protected $cfg;

    public function __construct(array $cfg) {
        $this->cfg = $cfg;
    }

    public function handleRequest(){

        $api = new APIController($this->cfg);
        $method = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // GET /api/process?UID=
        if (strpos($requestUri, 'process') !== false && $method === 'GET') {
            $fileName = $this->cfg['filesWithUIDs'][$_GET['UID']]['filename'];
            $filePath = $this->cfg['csvFolder'].'/'.$fileName;
            $api->createStatistics($filePath);
        }

        // POST /api/process
        if (strpos($requestUri, 'process') !== false && $method === 'POST') {
            if($_FILES['csv_file']['tmp_name'] != NULL) {
                $fileContent = file_get_contents($_FILES['csv_file']['tmp_name']);
                $api->createStatistics($fileContent);
            }
        }

        // Fallback for unmatched routes to just go to homepage
        include __DIR__.'/../assets/templates/index.php';
    }
}
