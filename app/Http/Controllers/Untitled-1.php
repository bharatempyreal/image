

Dropzone.options.dropzone =
         {
            maxFilesize: 12,
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
               return time+file.name;
            },

            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            timeout: 5000,
            addRemoveLinks: true,
            removedfile: function(file) 
            {
                var name = file.upload.filename;
               
                $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                    type: 'POST',
                    url: '{{ url("delete") }}',
                    data: {filename: name,
                        _tokan :"{{ csrf_token() }}"
                    },
                    success: function (data){
                        console.log("File deleted successfully!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function(file, response) 
            {
                console.log(response);
            },
            error: function(file, response)
            {
               return false;
            }
};




<div class="dropzone sortable dz-clickable sortable">
            <div class="dz-message">
                Drop files here or click to upload.
            </div>
            {{-- {{dd(storage_path())}} --}}
            @foreach ($images as $image )

            <div class="dz-preview" data-id="{{ $image->id }}" data-path="{{ $image->image }}">
                <img class="dropzone-thumbnail" src={{ url('app/public/uploads').'/'.$image->name }}>
                <a class="dz-remove" href="javascript:void(0);" data-remove="{{ $image->id }}">Remove file</a>
            </div>
            @endforeach

        </div>




        {{-- @foreach($image as $images)
                <img  class="dropzone-thumbnail" src="'{{ route('image.displayImage', $images->id)}}'" height="100px"
                width="100px">
                <a class="dz-remove" href="javascript:void(0);" data-remove="{{ $images->id }}">Remove file</a>
            </div>
            @endforeach --}}