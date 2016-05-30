{!! Form::open(['route' => ['ice.create'], 'method' => 'POST']) !!}

<div class="form-group">
    {!! Form::label('mount-name', 'Mountname:')!!}
    {!! Form::text('mount-name', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('password', 'Password:')!!}
    {!! Form::password('password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('max-listeners', 'Max Listeners:')!!}
    {!! Form::text('max-listeners', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('bitrate', 'Bitrate:')!!}
    {!! Form::text('bitrate', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin-user', 'Admin Username:')!!}
    {!! Form::text('admin-user', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin-mail', 'Admin Email:')!!}
    {!! Form::text('admin-mail', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin-password', 'Admin Password:')!!}
    {!! Form::password('admin-password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::submit('Hope!', ['class'=>'btn btn-primary form-control'] )!!}
</div>

{!! Form::close() !!}