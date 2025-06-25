<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وضع عدم الاتصال - نظام إدارة المستشفى</title>
    <link rel="stylesheet" href="/assets/css/material-dashboard.css">
    <style>
        .offline-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .offline-card {
            max-width: 500px;
            width: 90%;
            padding: 2rem;
            text-align: center;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .offline-icon {
            font-size: 48px;
            color: #fb8c00;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-card">
            <div class="offline-icon">
                <i class="material-icons">wifi_off</i>
            </div>
            <h2>أنت حالياً غير متصل بالإنترنت</h2>
            <p class="mt-3">لا تقلق، سيتم حفظ جميع البيانات التي تدخلها محلياً ومزامنتها تلقائياً عند عودة الاتصال.</p>
            <div class="mt-4">
                <button onclick="window.location.reload()" class="btn btn-warning">
                    إعادة المحاولة
                </button>
                <a href="/" class="btn btn-outline-secondary">
                    العودة للرئيسية
                </a>
            </div>
            <div class="mt-4 text-muted">
                <small>يمكنك الاستمرار في استخدام النظام في وضع عدم الاتصال</small>
            </div>
        </div>
    </div>

    <script>
        // Check connection status periodically
        setInterval(() => {
            if (navigator.onLine) {
                window.location.reload();
            }
        }, 5000);
    </script>
</body>
</html>