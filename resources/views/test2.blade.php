{!! Form::open(['route' => ['mount.create' , 'id'=>1], 'method' => 'POST']) !!}

<div class="form-group">
    {!! Form::label('mount_name', 'Mountname:')!!}
    {!! Form::text('mount_name', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('password', 'Password:')!!}
    {!! Form::password('password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('max_listeners', 'Max Listeners:')!!}
    {!! Form::text('max_listeners', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('bitrate', 'Bitrate:')!!}
    {!! Form::text('bitrate', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::submit('Hope!', ['class'=>'btn btn-primary form-control'] )!!}
</div>

{!! Form::close() !!}