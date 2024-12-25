<?php 

$request = $_SERVER['REQUEST_URI'];
$viewDir = "./views/";

switch ($request) {
    case '':
    case '/':
        require __DIR__ . $viewDir . 'dashboard.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    case '/ziyaretciler/':
        require __DIR__ . $viewDir . 'guests.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    case '/kiosk/':
        require __DIR__ . $viewDir . 'kiosk.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    case '/kullanicilar/':
        require __DIR__ . $viewDir . 'users.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    case '/stok/':
        require __DIR__ . $viewDir . 'stock.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    case '/odunc/':
        require __DIR__ . $viewDir . 'loan.php';
        require __DIR__ . $viewDir . 'footer.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . $viewDir . '404.php';
}
?>