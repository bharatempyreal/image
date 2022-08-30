@extends('layouts.app')

@section('style')
<style>
.dropzone .dz-preview .dz-image img {
    display: block;
    width: 100%;
}

.sortable {
    list-style-type: none;
    margin: 0;
    padding: 0;
    width: 100%;
    overflow: auto;
}

/*border: 1px SOLID #000;*/
.sortable {
    margin: 3px 3px 3px 0;
    padding: 1px;
    float: left;
    /*width: 120px; height: 120px;*/
    vertical-align: bottom;
    text-align: center;
}

.dropzone-thumbnail {
    width: 115px;
    cursor: move !important;
}

.dz-preview {
    cursor: move !important;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="container pt-5">
                <div class="mb-3">
                    <label class="form-label @error('productFiles') is-invalid @enderror">Images <span
                            class="text-danger"></span></label>
                    <!-- <div class="dropzone"></div> -->
                    <form method="post" action="{{ route('image') }}" id="dropzone" class="dropzone sortable dz-clickable sortable"
                        enctype="multipart/form-data">
                        @csrf
                    </form>

                </div>
            </div>




        </div>
    </div>
</div>

<div class="container">
    <table class="table" id="imagetable">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Name</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection

@section('script')

<script type="text/javascript">
// Dropzone.autoDiscover = false;
// var myDropzone;

Dropzone.autoDiscover = false;
var uploaded = false;

var dropzone = new Dropzone(".dropzone", {
    url: '{{ url("image") }}',
    paramName: "productFiles",
    parallelUploads: 5,
    uploadMultiple: true,
    acceptedFiles: ".jpg,.png,.webp",
    maxFilesize: 20,
    dictFileTooBig: "File too Big, please select a file less than 20mb",
    addRemoveLinks: true,
    dictRemoveFile: 'Remove file',
    timeout: 10000,

    init: function() {
        var myDropzone = this;

        $.ajax({
            type: "POST",
            url: "{{ route('getimg') }}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    $.each(data.images, function(key, value) {

                        var mockFile = {
                            name: value.name,
                            size: value.size,
                            id: value.id,
                        };
                        myDropzone.emit("addedfile", mockFile);
                        myDropzone.emit("thumbnail", mockFile, value.server);
                        myDropzone.emit("complete", mockFile);

                    });
                } else {
                    alert("Image not loaded");
                }
            }
        });


        this.on("error", function(file, message) {
            this.removeFile(file);
        });

        this.on('sendingmultiple', function(file, xhr, formData) {
            var data = $('#addfrm').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });

        this.on("successmultiple", function(multiple, response) {
            if ($.isEmptyObject(response.errors)) {
                if (response.status == true) {
                    Toast.fire({
                        icon: response.icon,
                        title: response.message
                    });
                    window.location.href = "{{ route('image')}}";
                } else {
                    Toast.fire({
                        icon: response.icon,
                        title: response.message
                    });
                }
            } else {
                var error = '';
                $(response.errors).each(function(row, val) {
                    error += '<li>' + val + '</li>';
                });
                if (error != '') {
                    error = '<ul>' + error + '</ul>';
                    setTimeout(function() {
                        Toast.fire('error', error);
                    }, 2000);
                } else {
                    Toast.fire('error', 'Error occurred!...');
                }
            }
        });
    },


    removedfile: function(file) {

        if (this.options.dictRemoveFile) {
            if (file.id != "") {
                var id = file.id;
            } else {
                var name = file.name;
            }

            if (confirm('Are you sure you want to delete this?')) {
                $.ajax({
                    type: "POST",
                    url: "{!! route('delete') !!}",
                    data: {
                        id: id,
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                }).done(function(data) {

                    if (data.success == true) {
                        // file.id.parentNode.removeChild(file);
                        // this.removeFile(file.id);
                        // $('div.dz-preview[data-id="'+id+'"]').remove()
                        console.log(data.message);

                    } else {
                        console.log(data.message);
                    }
                    var fileRef;


                    console.log(file.id);
                    return (fileRef = file.id) != null ? fileRef.parentNode.removeChild(file.id) :
                        void 0;
                }).fail(function(jqXHR, status, exception) {
                    if (jqXHR.status === 0) {
                        error = 'Not connected.\nPlease verify your network connection.';
                    } else if (jqXHR.status == 404) {
                        error = 'The requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        error = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        error = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        error = 'Time out error.';
                    } else if (exception === 'abort') {
                        error = 'Ajax request aborted.';
                    } else {
                        error = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.log(error);
                });
            }



        }
    }
});

$(".dropzone").sortable({
    items: '.dz-preview',
    cursor: 'move',
    opacity: 0.5,
    containment: '.dropzone',
    distance: 20,
    scroll: true,
    tolerance: 'pointer',
    stop: function(event, ui) {
        var path = [];
        var queue = dropzone.getAcceptedFiles();
        console.log(queue);
        $('#dropzone .dz-preview .dz-filename [data-dz-name]').each(function(count, el) {
            var name = el.getAttribute('data-name');
            queue.forEach(function(file) {
                if (file.name === name) {
                    newQueue.push(file);
                }
            });
        });

        // uploadzone.files = newQueue;




        // $('.dz-preview').each(function() {
        //     var path_id = $(this).data('id');
        //     var image = $(this).data('path');
        //     path.push({
        //         id: path_id,
        //         path: path
        //     });
        // });

        $.ajax({
            url: "{{ route('sortable') }}",
            type: 'POST',
            data: {
                path: path,
                _token: "{{ csrf_token() }}"
            },
        }).done(function(status) {
            var notification_type;
            if (status.success) {
                notification_type = 'success';
            } else {
                notification_type = 'error';
            }
        });
    }
});


// Dropzone.autoDiscover = false;
// var uploaded = false;

// var dropzone = new Dropzone(".dropzone", {
//     url: '{{ url("image") }}',
//     acceptedFiles: ".jpeg,.jpg,.png,.gif",
//     uploadMultiple: true,
//     addRemoveLinks: true,
//     maxFilesize: 12,
//     parallelUploads: 10,
//     sending: function(file, xhr, formData) {
//         formData.append("_token", $('[name=_token').val());

//     },
//     error: function(file, response) {

//         $(file.previewElement).find('.dz-error-message').remove();
//         $(file.previewElement).remove();

//         $(function() {
//             new PNotify({
//                 title: file.name + " was not uploaded!",
//                 text: response,
//                 type: "error",
//                 icon: false
//             });
//         });

//     },
//     success: function(file, response) {
//         console.log(response);
//         $('#dropzone').html(data);
//         $(file.previewElement).remove();
//         var notification_type;
//         if (response.success) {
//             var image = '<div class="dz-preview" data-id="' + response.id + '" data-path="' + response
//                 .image + '">';
//             image += '<img class="dropzone-thumbnail" src="'.route('image.displayImage'). + response.image +
//                 '">';
//             image += '<a class="dz-remove" href="javascript:void(0);" data-remove="' + response.id +
//                 '" data-path="' + response.image + '">Remove file</a>';
//             image += '</div>';
//             $('.dropzone').append(image);
//             notification_type = 'success';
//         } else {
//             notification_type = 'error';
//         }
//         // new PNotify({
//         // 	text: response.message,
//         // 	type: notification_type,
//         // 	icon: false
//         // });
//     }
// });



$(document).ready(function() {


    var table = $('#imagetable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('table') }}",
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'image',
                name: 'image'
                // render: function(data, type, row, meta) {
                //     return '<img  src="' + data + '" height="100px" width="100px">';
                // }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });


    $(document).on("click", ".delete", function() {
        // alert('hh');
        var action = $(this).data("action");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        if (confirm('Are you sure you want to delete this?')) {
            $.ajax({
                type: "DELETE",
                url: action,
                success: function(data) {
                    table.ajax.reload();
                },
            });
        }

    });
});
</script>
@endsection