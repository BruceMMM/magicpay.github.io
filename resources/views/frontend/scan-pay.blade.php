@extends('frontend.layouts.app')

@section('title','Scan & Pay')

@section('content')
<div class="scan-pay">
    <div class="card my-card">
        <div class="card-body text-center">
            @include('frontend.layouts.flash')

            <div class="text-center">
                <img src="{{asset('img/scan-and-pay.png')}}" alt="" style="width:200px;">
            </div>
            <p class="mb-3">Click button, put QR code in the frame and pay.</p>
            <button class="btn  btn-theme btn-sm" data-toggle="modal" data-target="#scanModal">Scan</button>


            <!-- Modal -->
            <div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="scanModallabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Scan & Pay</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video src="" id="scanner" width="100%" height="240px"></video>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')




    <script type="text/javascript" src="{{asset('frontend/js/instascan.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            let scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });
            scanner.addListener('scan', function (result) {
                if(result > 0){
                    scanner.stop();
                    $('#scanModal').modal('hide');

                    var to_phone = result;
                    window.location.replace(`scan-and-pay-form?to_phone=${to_phone}`);
                }
            });

            $('#scanModal').on('shown.bs.modal', function (event) {
                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    }
                });
            });

            $('#scanModal').on('hidden.bs.modal', function (event) {
                scanner.stop();
            });


        });

    </script>

@endsection
