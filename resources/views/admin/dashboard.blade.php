@extends('layouts.admin') 
@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">bed</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">إجمالي الأسرة</p>
            <h4 class="mb-0">{{ $beds_count }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span> سرير فارغ</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">hotel_class</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">نسبة الأسرة الفارغة</p>
            <h4 class="mb-0">{{ $beds_count > 0 ? round(($empty_beds / $beds_count) * 100, 1) : 0 }}%</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span> من أصل {{ $beds_count }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">person_add</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">دخول المرضى (شهري)</p>
            <h4 class="mb-0">{{ $patients_in_month }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">+5%</span> من الشهر الماضي</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">logout</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">خروج المرضى (شهري)</p>
            <h4 class="mb-0">{{ $patients_out_month }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-danger text-sm font-weight-bolder ms-1">-2%</span> مقارنة بالشهر الماضي</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4" id="stats-cards">
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="mb-0">عدد المرضى الجدد</h6>
          <p class="text-sm">تم تسجيل {{ $new_patients }} مريض جديد هذا الأسبوع</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto ms-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث منذ يومين</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="mb-0">عدد الأسرة المشغولة</h6>
          <p class="text-sm">تم إشغال {{ $occupied_beds }} سرير هذا الأسبوع</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto ms-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث منذ 4 دقائق</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mt-4 mb-3">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
            <h6 class="mb-0">عدد المرضى المترددين</h6>
          <p class="text-sm">تم تسجيل {{ $daily_visits }} زيارة اليوم</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto me-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث للتو</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="chart-bars-data" style="display: none;">{{ json_encode($chartBarsData ?? []) }}</div>
  <div id="chart-line-data" style="display: none;">{{ json_encode($chartLineData ?? []) }}</div>
  <div id="chart-line-tasks-data" style="display: none;">{{ json_encode($chartLineTasksData ?? []) }}</div>
</div>

@push('scripts')
<script>
// Wait for both DOM and Chart.js to be ready
function initializeCharts() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    console.log('Initializing charts...');
    
    // Configure Chart.js defaults for RTL
    Chart.defaults.font.family = "'Inter', 'Arial', sans-serif";
    Chart.defaults.color = '#ffffff';
    
    // Chart 1: مرضى جدد (أسبوعي)
    const ctx1 = document.getElementById("chart-bars");
    if (ctx1) {
        console.log('Creating weekly patients chart...');
        
        // Use fallback data first, then try to fetch real data
        const fallbackData = {
            labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            data: [{{ $new_patients ?? 0 }}, 3, 5, 2, 4, 6, 1]
        };
        
        const chart1 = new Chart(ctx1, {
            type: "bar",
            data: {
                labels: fallbackData.labels,
                datasets: [{
                    label: 'مرضى جدد',
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    data: fallbackData.data,
                    maxBarThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                return 'مرضى جدد: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    }
                }
            }
        });
        
        // Try to fetch real data and update chart
        fetch('{{ route("charts.weekly.patients") }}')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(chartData => {
                console.log('Updating chart with real data:', chartData);
                chart1.data.labels = chartData.labels;
                chart1.data.datasets[0].data = chartData.data;
                chart1.update();
            })
            .catch(error => {
                console.error('Error fetching weekly patients data:', error);
            });
    }

    // Chart 2: الأسرة المشغولة (شهري)
    const ctx2 = document.getElementById("chart-line");
    if (ctx2) {
        console.log('Creating monthly beds chart...');
        
        const fallbackData2 = {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            data: [{{ $occupied_beds ?? 0 }}, 15, 20, 18, 22, 25, 20, 18, 16, 19, 21, 17]
        };
        
        const chart2 = new Chart(ctx2, {
            type: "line",
            data: {
                labels: fallbackData2.labels,
                datasets: [{
                    label: 'أسرة مشغولة',
                    borderColor: 'rgba(255, 255, 255, 0.9)',
                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(255, 255, 255, 1)',
                    pointBorderColor: 'rgba(255, 255, 255, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    data: fallbackData2.data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                return 'أسرة مشغولة: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    }
                }
            }
        });
        
        // Try to fetch real data and update chart
        fetch('{{ route("charts.monthly.beds") }}')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(chartData => {
                console.log('Updating chart with real data:', chartData);
                chart2.data.labels = chartData.labels;
                chart2.data.datasets[0].data = chartData.data;
                chart2.update();
            })
            .catch(error => {
                console.error('Error fetching monthly beds data:', error);
            });
    }

    // Chart 3: الزيارات اليومية
    const ctx3 = document.getElementById("chart-line-tasks");
    if (ctx3) {
        console.log('Creating daily visits chart...');
        
        const fallbackData3 = {
            labels: ['6 ص', '9 ص', '12 ظ', '3 م', '6 م', '9 م', '12 ص'],
            data: [2, 5, {{ $daily_visits ?? 0 }}, 8, 12, 6, 3]
        };
        
        const chart3 = new Chart(ctx3, {
            type: "line",
            data: {
                labels: fallbackData3.labels,
                datasets: [{
                    label: 'زيارات',
                    borderColor: 'rgba(255, 255, 255, 0.9)',
                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(255, 255, 255, 1)',
                    pointBorderColor: 'rgba(255, 255, 255, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                    data: fallbackData3.data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                return 'زيارات: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#ffffff',
                            font: {
                                family: 'Inter'
                            }
                        }
                    }
                }
            }
        });
        
        // Try to fetch real data and update chart
        fetch('{{ route("charts.daily.visits") }}')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(chartData => {
                console.log('Updating chart with real data:', chartData);
                chart3.data.labels = chartData.labels;
                chart3.data.datasets[0].data = chartData.data;
                chart3.update();
            })
            .catch(error => {
                console.error('Error fetching daily visits data:', error);
            });
    }
}

// Initialize charts when DOM is ready and Chart.js is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is already loaded
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    } else {
        // Wait for Chart.js to load
        let attempts = 0;
        const maxAttempts = 50; // 5 seconds
        const checkChart = setInterval(() => {
            attempts++;
            if (typeof Chart !== 'undefined') {
                clearInterval(checkChart);
                initializeCharts();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkChart);
                console.error('Chart.js failed to load after 5 seconds');
            }
        }, 100);
    }
});

// Also try when window loads as fallback
window.addEventListener('load', function() {
    if (typeof Chart !== 'undefined') {
        // Only initialize if not already done
        if (!document.querySelector('#chart-bars canvas')) {
            setTimeout(initializeCharts, 500);
        }
    }
});

// Debug information
console.log('Dashboard script loaded');
console.log('Chart.js available:', typeof Chart !== 'undefined');
console.log('jQuery available:', typeof $ !== 'undefined');
</script>
@endpush
@endsection