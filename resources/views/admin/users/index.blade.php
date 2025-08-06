@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-dynamic mb-4">
                <label class="form-label">ابحث عن مستخدم بالاسم او الايميل ...</label>
                <input type="text" id="searchQuery" class="form-control">
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card my-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>إدارة المستخدمين</h6>
                    <div class="d-flex gap-2 align-items-center">
                        <!-- Sort Options -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons opacity-10">sort</i>
                                ترتيب حسب
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><h6 class="dropdown-header">ترتيب المستخدمين</h6></li>
                                <li><a class="dropdown-item" href="{{ route('users.index', ['sort_by' => 'name']) }}">
                                    <i class="material-icons opacity-10">abc</i> الاسم
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('users.index', ['sort_by' => 'email']) }}">
                                    <i class="material-icons opacity-10">email</i> الايميل
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('users.index', ['sort_by' => 'role']) }}">
                                    <i class="material-icons opacity-10">admin_panel_settings</i> الدور
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('users.index', ['sort_by' => 'created_at']) }}">
                                    <i class="material-icons opacity-10">schedule</i> تاريخ الإنشاء
                                </a></li>
                            </ul>
                        </div>

                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="material-icons opacity-10">person_add</i>
                            إضافة مستخدم جديد
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            <i class="material-icons opacity-10">check_circle</i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                            <i class="material-icons opacity-10">error</i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end" id="usersTable">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>الاسم</th>
                                    <th>الايميل</th>
                                    <th>الدور</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <img src="{{ asset('assets/img/user-avatar.png') }}" 
                                             alt="Avatar" 
                                             class="avatar" 
                                             style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @switch($user->role)
                                            @case('admin')
                                                <span class="badge bg-gradient-danger">مدير</span>
                                                @break
                                            @case('manager')
                                                <span class="badge bg-gradient-warning">مدير فرعي</span>
                                                @break
                                            @case('reception')
                                                <span class="badge bg-gradient-info">استقبال</span>
                                                @break
                                            @default
                                                <span class="badge bg-gradient-secondary">غير محدد</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm bg-gradient-info">
                                            <i class="material-icons opacity-10">visibility</i>
                                            عرض
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm bg-gradient-warning">
                                            <i class="material-icons opacity-10">edit</i>
                                            تعديل
                                        </a>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.reset-password', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm bg-gradient-primary">
                                                    <i class="material-icons opacity-10">lock_reset</i>
                                                    إعادة تعيين كلمة المرور
                                                </button>
                                            </form>

                                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-gradient-danger">
                                                    <i class="material-icons opacity-10">delete</i>
                                                    حذف
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-gradient-success">حسابك الحالي</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا يوجد مستخدمين</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination links --}}
                    <div class="d-flex justify-content-center">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchQueryInput = document.getElementById('searchQuery');
    const usersTableBody = document.getElementById('usersTableBody');

    if (searchQueryInput) {
        searchQueryInput.addEventListener('input', function () {
            const query = this.value;

            // Show loading spinner
            usersTableBody.innerHTML = '<tr><td colspan="6" class="text-center">جاري البحث...</td></tr>';

            // Send AJAX request to search users
            fetch(`/admin/users/ajax-search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear the table body
                    usersTableBody.innerHTML = '';

                    // Populate the table with the search results
                    if (data.length > 0) {
                        data.forEach(user => {
                            let roleText = '';
                            let roleBadge = '';
                            
                            switch(user.role) {
                                case 'admin':
                                    roleText = 'مدير';
                                    roleBadge = 'bg-gradient-danger';
                                    break;
                                case 'manager':
                                    roleText = 'مدير فرعي';
                                    roleBadge = 'bg-gradient-warning';
                                    break;
                                case 'reception':
                                    roleText = 'استقبال';
                                    roleBadge = 'bg-gradient-info';
                                    break;
                                default:
                                    roleText = 'غير محدد';
                                    roleBadge = 'bg-gradient-secondary';
                            }

                            const isCurrentUser = user.id == {{ auth()->id() }};
                            
                            const row = `
                                <tr>
                                    <td>
                                        <img src="/assets/img/user-avatar.png" alt="Avatar" class="avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <span class="badge ${roleBadge}">${roleText}</span>
                                    </td>
                                    <td>${new Date(user.created_at).toLocaleDateString('ar-EG')}</td>
                                    <td>
                                        <a href="/admin/users/${user.id}" class="btn btn-sm bg-gradient-info">
                                            <i class="material-icons opacity-10">visibility</i>
                                            عرض
                                        </a>
                                        <a href="/admin/users/${user.id}/edit" class="btn btn-sm bg-gradient-warning">
                                            <i class="material-icons opacity-10">edit</i>
                                            تعديل
                                        </a>
                                        
                                        ${!isCurrentUser ? `
                                            <form action="/admin/users/${user.id}/reset-password" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟');">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <button type="submit" class="btn btn-sm bg-gradient-primary">
                                                    <i class="material-icons opacity-10">lock_reset</i>
                                                    إعادة تعيين كلمة المرور
                                                </button>
                                            </form>

                                            <form action="/admin/users/${user.id}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm bg-gradient-danger">
                                                    <i class="material-icons opacity-10">delete</i>
                                                    حذف
                                                </button>
                                            </form>
                                        ` : '<span class="badge bg-gradient-success">حسابك الحالي</span>'}
                                    </td>
                                </tr>
                            `;
                            usersTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        usersTableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">لا توجد نتائج مطابقة.</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    usersTableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger">حدث خطأ أثناء البحث.</td>
                        </tr>
                    `;
                });
        });
    }
});
</script>
@endsection