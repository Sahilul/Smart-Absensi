<?php
/**
 * ValidateController
 * Public controller for QR code validation (no login required)
 */

class ValidateController extends Controller {
    
    public function index() {
        // Simply load the standalone validate.php
        // But we need to be in the right directory context
        chdir(APPROOT . '/public');
        require APPROOT . '/public/validate.php';
        exit;
    }
}
