<div class="row">
    <div class="col-sm-12">
        <div class="text-center">
            <ul  class="nav nav-pills nav-injuries btn-sm" style="display: inline-block;">
                <li class="<?php if(Request::segment(3) == 'index') echo 'active'; ?> ">
                    <a href="{{  URL::to('insurances/reports/index') }}">Generowanie raportów</a>
                </li>

                <li class="separated">|</li>

                <li class="<?php if(Request::segment(3) == 'archive') echo 'active'; ?> ">
                    <a href="{{  URL::to('insurances/reports/archive') }}" >Wykaz wygenerowanych raportów</a>
                </li>

                <li class="separated">|</li>

                <li class="<?php if(Request::segment(3) == 'sheets') echo 'active'; ?> ">
                    <a href="{{  URL::to('insurances/reports/sheets') }}" >Generowanie zestawień</a>
                </li>
            </ul>
        </div>
    </div>
</div>