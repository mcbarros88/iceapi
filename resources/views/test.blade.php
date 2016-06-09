<h1>Crea Icecast e mountpoint</h1>

{!! Form::open(['route' => ['ice.create'], 'method' => 'POST']) !!}

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
    {!! Form::label('admin_user', 'Admin Username:')!!}
    {!! Form::text('admin_user', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin_mail', 'Admin Email:')!!}
    {!! Form::text('admin_mail', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin_password', 'Admin Password:')!!}
    {!! Form::password('admin_password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::submit('Hope!', ['class'=>'btn btn-primary form-control'] )!!}
</div>

{!! Form::close() !!}