 <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  </div>
  <div class="modal-body" style="overflow:hidden;">
      @if(in_array(mb_strtolower($ext), ['jpeg', 'jpg', 'png' ,'gif', 'bmp']))
            <img src="{{ url('injuries/preview-doc', [$id, $type]) }}" class="img-rounded" style="max-width: 100%;">
      @elseif(in_array(mb_strtolower($ext), ['tiff', 'tif']))
            <div class="image-body" style="height: 80vh; overflow: auto;">

            </div>
      @else
            <iframe style="width:100%; border: none; height:50vw;" src="{{ url('injuries/preview-doc', [$id, $type]) }}"></iframe>
      @endif
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
  </div>

 @if(in_array(mb_strtolower($ext), ['tiff', 'tif']))
     <script>
         $(function () {
             Tiff.initialize({TOTAL_MEMORY: 16777216 * 10});
             var xhr = new XMLHttpRequest();
             xhr.open('GET', '{{ url('injuries/preview-doc', [$id, $type]) }}');
             xhr.responseType = 'arraybuffer';
             xhr.onload = function (e) {
                 var buffer = xhr.response;
                 var tiff = new Tiff({buffer: buffer});
                 for (var i = 0, len = tiff.countDirectory(); i < len; ++i) {
                     tiff.setDirectory(i);
                     var canvas = tiff.toCanvas();

                     $('.image-body').append(canvas);
                 }

                 $('.image-body canvas').each(function(){
                     $(this).css('width', '100%');
                 });
             };
             xhr.send();
         });
     </script>
 @endif
