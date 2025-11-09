<?php

/**
 * File: app/core/App.php
 * Core routing class with Public Monitoring support
 * COPAS SELURUH FILE INI KE: app/core/App.php
 */
class App {
    protected $controller = 'AuthController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        require_once APPROOT . '/config/database.php';
        require_once APPROOT . '/app/core/Database.php';
        require_once APPROOT . '/app/core/Controller.php';
        require_once APPROOT . '/app/core/Flasher.php';
        require_once APPROOT . '/app/core/InputValidator.php'; // Load validator

        $url = $this->parseURL();
        
        // DEBUG: Log URL parsing
        error_log("App.php - URL parsed: " . print_r($url, true));

        // TAMBAHAN: Handle Public Routes (no login required)
        // ================================================================
        // 1. Public Monitoring
        if (isset($url[0]) && ($url[0] == 'monitoring' || ($url[0] == 'public' && isset($url[1]) && $url[1] == 'monitoring'))) {
            error_log("App.php - Public monitoring route detected");
            $this->handlePublicMonitoring($url);
            return; // PENTING: Exit setelah handle monitoring
        }
        
        // 2. Validasi Rapor (QR Code validation - PUBLIC ACCESS)
        if (isset($url[0]) && $url[0] == 'validasiRapor') {
            error_log("App.php - Validasi Rapor route detected (PUBLIC)");
            $this->handlePublicRoute('ValidasiRaporController', $url);
            return;
        }
        // ================================================================

        // EXISTING CONTROLLER ROUTING - TETAP SAMA
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            error_log("App.php - Looking for controller: " . $controllerName);
            
            if (file_exists(APPROOT . '/app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
                error_log("App.php - Controller found: " . $controllerName);
            } else {
                error_log("App.php - Controller NOT found: " . $controllerName);
            }
        }

        require_once APPROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Method routing
        if (isset($url[1])) {
            $methodName = $url[1];
            error_log("App.php - Looking for method: " . $methodName);
            
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
                error_log("App.php - Method found: " . $methodName);
            } else {
                error_log("App.php - Method NOT found: " . $methodName . " - Available methods: " . implode(', ', get_class_methods($this->controller)));
            }
        }

        // Parameters
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        error_log("App.php - Final routing: Controller=" . get_class($this->controller) . ", Method=" . $this->method . ", Params=" . print_r($this->params, true));

        // Call the method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // TAMBAHAN: Handler untuk Public Monitoring
    // ================================================================
    private function handlePublicMonitoring($url)
    {
        error_log("App.php - handlePublicMonitoring called with URL: " . print_r($url, true));
        
        // Check if PublicController exists
        $publicControllerPath = APPROOT . '/app/controllers/PublicController.php';
        if (!file_exists($publicControllerPath)) {
            error_log("App.php - PublicController.php NOT found at: " . $publicControllerPath);
            $this->showError('PublicController.php tidak ditemukan di: ' . $publicControllerPath . '<br><br>Pastikan file sudah dibuat dengan benar.');
            return;
        }

        // Load PublicController
        require_once $publicControllerPath;
        
        // Check if class exists
        if (!class_exists('PublicController')) {
            error_log("App.php - PublicController class NOT found after including file");
            $this->showError('Class PublicController tidak ditemukan. Periksa syntax di file PublicController.php');
            return;
        }

        try {
            $controller = new PublicController();
            error_log("App.php - PublicController instantiated successfully");

            // Determine action based on URL structure
            if (isset($url[1]) && $url[0] == 'public') {
                // URL: /public/monitoring/action
                $action = isset($url[2]) ? $url[2] : 'index';
                error_log("App.php - Public URL format, action: " . $action);
            } else {
                // URL: /monitoring/action  
                $action = isset($url[1]) ? $url[1] : 'index';
                error_log("App.php - Direct monitoring URL format, action: " . $action);
            }

            // Route to appropriate method
            switch ($action) {
                case 'api':
                    $this->handleMonitoringAPI($controller, $url);
                    break;
                    
                case 'export':
                    error_log("App.php - Calling exportCSV");
                    $controller->exportCSV();
                    break;
                    
                case 'test':
                    error_log("App.php - Calling test method");
                    if (method_exists($controller, 'test')) {
                        $controller->test();
                    } else {
                        echo "<h1>PublicController Test</h1>";
                        echo "<p>Routing berhasil! PublicController dapat diakses.</p>";
                        echo "<p><a href='" . BASEURL . "/monitoring'>Ke Halaman Monitoring</a></p>";
                    }
                    break;
                    
                case 'index':
                case 'monitoring':
                default:
                    error_log("App.php - Calling monitoringAbsensi (default)");
                    $controller->monitoringAbsensi();
                    break;
            }

        } catch (Exception $e) {
            error_log("App.php - Exception in handlePublicMonitoring: " . $e->getMessage());
            $this->showError('Error dalam PublicController: ' . $e->getMessage());
        }
    }

    // TAMBAHAN: Handler untuk route publik lainnya (validasi rapor, dll)
    // ================================================================
    private function handlePublicRoute($controllerName, $url)
    {
        error_log("App.php - handlePublicRoute called: " . $controllerName);
        
        $controllerPath = APPROOT . '/app/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerPath)) {
            error_log("App.php - Controller NOT found: " . $controllerPath);
            $this->showError('Controller tidak ditemukan: ' . $controllerName);
            return;
        }

        require_once $controllerPath;
        
        if (!class_exists($controllerName)) {
            error_log("App.php - Class NOT found: " . $controllerName);
            $this->showError('Class tidak ditemukan: ' . $controllerName);
            return;
        }

        try {
            $controller = new $controllerName();
            
            // Ambil method dari URL (default: index)
            $method = isset($url[1]) ? $url[1] : 'index';
            
            // Ambil parameters
            $params = [];
            if (count($url) > 2) {
                $params = array_slice($url, 2);
            } elseif (count($url) > 1 && $url[1] !== $method) {
                // Jika URL hanya 2 segment dan segment ke-2 bukan method,
                // maka segment ke-2 adalah parameter untuk method index
                $params = [$url[1]];
                $method = 'index';
            }
            
            error_log("App.php - Public route: method={$method}, params=" . print_r($params, true));
            
            if (method_exists($controller, $method)) {
                call_user_func_array([$controller, $method], $params);
            } else {
                error_log("App.php - Method NOT found: " . $method);
                $this->showError('Method tidak ditemukan: ' . $method . ' di ' . $controllerName);
            }
            
        } catch (Exception $e) {
            error_log("App.php - Exception in handlePublicRoute: " . $e->getMessage());
            $this->showError('Error: ' . $e->getMessage());
        }
    }
    // ================================================================

    private function handleMonitoringAPI($controller, $url)
    {
        // Determine API method from URL
        if (isset($url[1]) && $url[0] == 'public') {
            // URL: /public/monitoring/api/method
            $apiMethod = isset($url[3]) ? $url[3] : '';
        } else {
            // URL: /monitoring/api/method
            $apiMethod = isset($url[2]) ? $url[2] : '';
        }

        error_log("App.php - API method: " . $apiMethod);

        switch ($apiMethod) {
            case 'getStudentData':
                $controller->apiGetStudentData();
                break;
            case 'getStudentDetail':
                $controller->apiGetStudentDetail();
                break;
            case 'getTrendAnalysis':
                $controller->apiGetTrendAnalysis();
                break;
            case 'getAttendanceChart':
                $controller->apiGetAttendanceChart();
                break;
            default:
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'API endpoint not found: ' . $apiMethod,
                    'available_endpoints' => [
                        'getStudentData',
                        'getStudentDetail', 
                        'getTrendAnalysis',
                        'getAttendanceChart'
                    ]
                ]);
        }
    }

    private function showError($message)
    {
        http_response_code(500);
        echo "<!DOCTYPE html><html lang='id'><head>";
        echo "<title>Error Sistem</title>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<style>";
        echo "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 20px; }";
        echo ".error-container { max-width: 600px; margin: 50px auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }";
        echo ".error-header { background: #e11d48; color: white; padding: 20px; }";
        echo ".error-body { padding: 20px; }";
        echo ".error-footer { background: #f8fafc; padding: 15px 20px; border-top: 1px solid #e2e8f0; }";
        echo "h1 { margin: 0; font-size: 1.5rem; }";
        echo "p { color: #64748b; line-height: 1.6; }";
        echo ".btn { background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; }";
        echo ".btn:hover { background: #2563eb; }";
        echo "code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-family: 'Courier New', monospace; }";
        echo "</style>";
        echo "</head><body>";
        echo "<div class='error-container'>";
        echo "<div class='error-header'>";
        echo "<h1>⚠️ Error Sistem Monitoring</h1>";
        echo "</div>";
        echo "<div class='error-body'>";
        echo "<p>" . $message . "</p>";
        echo "<p><strong>Langkah troubleshooting:</strong></p>";
        echo "<ol>";
        echo "<li>Pastikan file <code>PublicController.php</code> ada di folder <code>app/controllers/</code></li>";
        echo "<li>Pastikan file <code>PublicAbsensi_model.php</code> ada di folder <code>app/models/</code></li>";
        echo "<li>Pastikan folder <code>app/views/public/</code> sudah dibuat</li>";
        echo "<li>Periksa log error untuk detail lebih lanjut</li>";
        echo "</ol>";
        echo "</div>";
        echo "<div class='error-footer'>";
        echo "<a href='" . BASEURL . "/auth/login' class='btn'>Kembali ke Login</a> ";
        echo "<a href='" . BASEURL . "/monitoring/test' class='btn' style='background: #10b981;'>Test Monitoring</a>";
        echo "</div>";
        echo "</div>";
        echo "</body></html>";
    }
    // ================================================================

    public function parseURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }

    // TAMBAHAN: Helper method untuk debugging routing
    // ================================================================
    public static function debugRouting($message = '')
    {
        if (!empty($message)) {
            error_log("ROUTING DEBUG: " . $message);
        }
        
        error_log("ROUTING DEBUG - Current URL: " . ($_SERVER['REQUEST_URI'] ?? 'undefined'));
        error_log("ROUTING DEBUG - GET params: " . print_r($_GET, true));
        error_log("ROUTING DEBUG - BASEURL: " . (defined('BASEURL') ? BASEURL : 'undefined'));
        
        // Show debug info in development
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo "<!-- DEBUG: " . $message . " -->\n";
        }
    }
    // ================================================================
}