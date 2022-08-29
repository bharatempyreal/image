@extends('layouts.app')

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
                <form method="post" class="dropzone" id="dropzone" action="{{url('image')}}" enctype="multipart/form-data">
                    @csrf
                </form>
                @foreach($image as $images)
                <img  class="dropzone-thumbnail" src="'{{ route('image.displayImage', $images->name)}}'" height="100px" width="100px">
					<a class="dz-remove" href="javascript:void(0);" data-remove="{{ $images->id }}">Remove file</a>
			    </div>
			@endforeach
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

Dropzone.autoDiscover = false;
        	var uploaded = false;

			var dropzone = new Dropzone(".dropzone", {
				url: '{{ url("image") }}',
				acceptedFiles: ".jpeg,.jpg,.png,.gif",
				uploadMultiple: true,
				addRemoveLinks: true,
				maxFilesize: 12,
				parallelUploads: 10,
				sending: function(file, xhr, formData) {
			        formData.append("_token", $('[name=_token').val());
			        
			    },
			    error: function(file, response) {
			    	
		            $(file.previewElement).find('.dz-error-message').remove();
		            $(file.previewElement).remove();

		            $(function(){
		              new PNotify({
		                title: file.name+" was not uploaded!",
		                text: response,
		                type: "error",
		                icon: false
		              });
		            });

			    },
			    success : function(file, response) {
                    console.log(response);
					$(file.previewElement).remove();
			        var notification_type;
					if (response.success) {
						var image = '<div class="dz-preview" data-id="'+response.id+'" data-path="'+response.image+'">';
							image += '<img class="dropzone-thumbnail" src="'.route('image.displayImage'). +response.image+'">';
							image += '<a class="dz-remove" href="javascript:void(0);" data-remove="'+response.id+'" data-path="'+response.image+'">Remove file</a>';
							image += '</div>';
						$('.dropzone').append(image);
						notification_type = 'success';
					} else {
						notification_type = 'error';
					}
					// new PNotify({
					// 	text: response.message,
					// 	type: notification_type,
					// 	icon: false
					// });
			    }
			});



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