<div class="tab-pane fade " id="premiums">



    @if($injury->sap)
        <div class="row">
            <div class="col-sm-12">
                <form action="{{ URL::to('injuries/sync-sap-premiums', [$injury->id]) }}" method="post">
                    {{Form::token()}}
                    <button type="submit" class="btn btn-xs btn-primary"><i class="fa fa-chain fa-fw"></i> Sprawdź nowe dopłaty z SAP</button>
                </form>
            </div>
        </div>
        <hr>

        @if($injury->sap->kwotaOdsz > 0)
            <p class="clearfix ">

                <span class="label label-primary">wypłata bez dopłat z SAP</span> |

                <strong>
                    {{ $injury->sap->kwotaOdsz  }}
                </strong>

                <hr class="short"/>

            </p>
        @endif

        @foreach($injury->sapPremiums as $premium)
            <p class="clearfix ">
                <span class="btn btn-danger btn-xs modal-open" target="{{ URL::to('injuries/delete-premium', array($premium->id)) }}"  data-toggle="modal" data-target="#modal" >
                    <i class="fa fa-trash-o fa-fw"></i> usuń z SAP
                </span>
                @if($premium->injuryCompensation)
                    <span class="label label-info">systemowa dopłata</span> |
                @else
                    <span class="label label-danger">dopłata z SAP</span> |
                @endif  
                <strong>
                    nr raty:
                    {{ $premium->nrRaty }} 
                </strong>

                <em>
                    <label>data dopłaty:</label> {{ $premium->dataDpl }}
                </em>

                <em>
                    <label>kwota dopłaty:</label> {{ $premium->kwDpl }}
                </em>

                <em>
                    <label>rejestrujący:</label> {{ $premium->unameRej }}
                </em>

                <em>
                    <label>data wpisu:</label> {{ $premium->dataRej }}
                </em>
                
                <hr class="short"/>

            </p>
        @endforeach
    @else
        <h3 class="text-center">Szkoda nie wysłana do SAP</h3>
    @endif
</div>
