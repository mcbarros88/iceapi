{!! Form::open(['route' => ['ice.edit' , 'id'=>1], 'method' => 'put']) !!}

<div class="form-group">
    {!! Form::label('admin_user', 'Admin user:')!!}
    {!! Form::text('admin_user', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin_password', 'Admin password:')!!}
    {!! Form::password('admin_password', null, ['class'=>'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('admin_mail', 'Admin mail:')!!}
    {!! Form::text('admin_mail', null, ['class'=>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::submit('Hope!', ['class'=>'btn btn-primary form-control'] )!!}
</div>


{!! Form::close() !!}