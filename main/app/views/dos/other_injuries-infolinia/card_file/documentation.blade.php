<div class="tab-pane fade in " id="documentation">

        <div class="row">
          <div class="col-sm-8  col-sm-offset-2 ">
          <table class="table table-hover" >
            @foreach($documents as $k => $v)
              <tr>
                <td width="10px">{{++$k}}.</td>
                <td width="180px" class="download_doc_td">
                  @if($v->type == 2)
                    <i>dok. wgrany</i>
                  @else
                    <i>dok. wygenerowany</i>
                  @endif
                </td>
                <td>
                  @if($v->type == 2)
                  {{ Config::get('definition.fileCategory.'.$v->category)}}<br>
                  <i>{{ $v->name }}</i>
                  @else
                  {{ $v->document_type()->first()->name }}
                    @if($v->name != '')
                      <br>
                      <i>{{ $v->name }}</i>
                    @endif
                  @endif
                </td>
                <Td>
                  {{ $v->user->name }}
                </td>
                <Td>
                  {{substr($v->created_at, 0, -3)}}
                </td>

              </tr>
            @endforeach
          </table>
          </div>
        </div>
      </div>