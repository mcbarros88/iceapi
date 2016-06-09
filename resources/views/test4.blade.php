<h1>Modifica Mountpoint</h1>

{!! Form::open(['route' => ['mount.edit' , 'id'=>1], 'method' => 'put']) !!}

<div class="form-group">
    {!! Form::label('max_listeners', 'Max listeners:')!!}
    {!! Form::text('max_listeners', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('password', 'Password:')!!}
    {!! Form::password('password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('bitrate', 'Bitrate:')!!}
    {!! Form::text('bitrate', null, ['class'=>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::submit('Hope!', ['class'=>'btn btn-primary form-control'] )!!}
</div>


{!! Form::close() !!}