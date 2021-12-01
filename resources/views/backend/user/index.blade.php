@extends('backend.layouts.app')

@section('title','User')

@section('user-active','mm-active')

@section('content')
<div class="app-page-title" style="padding: 10px 30px; margin:-30px -30px 0px;">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-users icon-gradient bg-mean-fruit">
                </i>
            </div>
            <div>User</div>
        </div>
    </div>
</div>



<div class="content py-3">
    <div class="container">

        <div class="pb-3">
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"> Create User</i>
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered datatable">
                    <thead class="bg-light">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Login Time</th>
                        <th>Create Time</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
 <script>
     $(document).ready(function() {
        var table =$('.datatable').DataTable( {
            processing: true,
            serverSide: true,
            ajax: "/admin/user/datatable/ssd",
            columns: [
                {
                    data: "name",
                    name: "name",
                },
                {
                    data: "email",
                    name: "email",
                },
                {
                    data: "phone",
                    name: "phone",
                },
                {
                    data: "ip",
                    name: "ip",
                },
                {
                    data: "user_agent",
                    name: "user_agent",
                    sortable: false,
                    searchable: false,
                },
                {
                    data: "login_at",
                    name: "login_at"
                },
                {
                    data: "created_at",
                    name: "created_at"
                },
                {
                    data: "action",
                    name: "action",
                    sortable: false,
                    searchable: false,
                }
            ],
            order: [[5,"desc"]]
        });

        $(document).on('click','.delete',function(e){
            e.preventDefault();

            var id= $(this).data('id');

            Swal.fire({
                title: 'Are you sure you want to delete?',
                showCancelButton: true,
                confirmButtonText: `Confirm`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $.ajax({
                        url : '/admin/user/' + id,
                        type : 'DELETE',
                        success: function(){
                            table.ajax.reload()
                        }
                    })
                }
            })
        });
    } );
 </script>
@endsection


