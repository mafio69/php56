
<?php 
if(isset($owner_template_data)) $custom_style = $owner_template_data['style'];
else  $custom_style = "";
?>

<p style={{'line-height:1.5;'.$custom_style}}>{{($owner->data()->where('parameter_id', 1)->first() ) ? $owner->data()->where('parameter_id', 1)
    ->first()->value : '---'}}
    <br>{{($owner->data()->where('parameter_id', 2)->first() ) ? $owner->data()->where('parameter_id', 2)->first()
    ->value : '---'}}
    {{($owner->data()->where('parameter_id', 3)->first() ) ? $owner->data()->where('parameter_id', 3)->first()
    ->value : '---'}}
    <br>{{($owner->data()->where('parameter_id', 13)->first() ) ? $owner->data()->where('parameter_id', 13)->first()
    ->value : '---'}}</p>