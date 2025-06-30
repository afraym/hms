@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>استيراد المرضى</h6>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">استيراد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection