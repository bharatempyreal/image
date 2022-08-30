Dropzone.autoDiscover = false;
        	var uploaded = false;

			var dropzone = new Dropzone(".dropzone", {
				url: '{{ url("image") }}',
				acceptedFiles: ".jpeg,.jpg,.png,.gif",
				uploadMultiple: true,
				addRemoveLinks: true,
				// autoProcessQueue: false,
				maxFilesize: 12,
				parallelUploads: 10,
				// previewTemplate:
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