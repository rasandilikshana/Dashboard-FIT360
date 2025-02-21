@push('scripts')
    <script>
        (function($) {
            $(document).ready(function(){
                changeValues();

                $('input[name="is_paid"]').change(function () {
                    changeValues();
                });

                $(document).on('change', '#workout_id', function () {                    
                    changeValues();
                });

                function changeValues() {
                    var is_paid_val = $('input[name="is_paid"]:checked').val();
                    if ( is_paid_val == 1 ) {
                        $('.is_paid_price').show();
                        $('#price').prop('required', true);
                    }else{
                        $('.is_paid_price').hide();
                        $('#price').prop('required', false);
                    }

                    var class_id = $('#workout_id').val();
                    if ( class_id == 'other' ) {
                        $('.workout_title').show();
                        $('#workout_title').prop('required', true);
                    }else{
                        $('.workout_title').hide();
                        $('#workout_title').prop('required', false);
                    }
                }
            });
        })(jQuery);
    </script>
@endpush
<x-app-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, [ 'route' => ['classschedule.update', $id], 'method' => 'patch']) !!}
        @else
            {!! Form::open(['route' => ['classschedule.store'], 'method' => 'post']) !!}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('classschedule.index') }} " class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ Form::label('class_name', __('message.class_name').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('class_name', old('class_name'),[ 'placeholder' => __('message.class_name'),'class' =>'form-control','required']) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ Form::label('workout_id', __('message.workout').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false) }} 
                                {{ Form::select('workout_id',isset($id) ? $workout_id : [], old('workout_id'), [
                                        'class' => 'select2js form-group workout',
                                        'data-placeholder' => __('message.select_name',[ 'select' => __('message.workout') ]),
                                        'data-ajax--url' => route('ajax-list', ['type' => 'workout' , 'sub_type' => 'class_schedule_workout']),
                                    ])
                                }}
                            </div>
                            <div class="form-group col-md-4 workout_title">
                                {{ Form::label('workout_title', __('message.workout_title').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('workout_title', old('workout_title'),[ 'placeholder' => __('message.workout_title'),'class' =>'form-control','required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('start_date', __('message.start_date').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('start_date', old('start_date'),[ 'placeholder' => __('message.start_date'), 'class' =>'maxdatepicker form-control', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('end_date', __('message.end_date').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('end_date', old('end_date'),[ 'placeholder' => __('message.end_date'), 'class' =>'maxdatepicker form-control', 'required']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('start_time', __('message.start_time').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('start_time', old('start_time'),[ 'placeholder' => __('message.start_time'), 'class' =>'timepicker24 form-control', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('end_time', __('message.end_time').' <span class="text-danger">*</span>',[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('end_time', old('end_time'),[ 'placeholder' => __('message.end_time'), 'class' =>'timepicker24 form-control', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('name', __('message.name'),[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::text('name', old('name'),[ 'placeholder' => __('message.name'),'class' =>'form-control']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('link', __('message.link'),[ 'class' => 'form-control-label' ], false ) }}
                                {{ Form::url('link', old('link'),[ 'placeholder' => __('message.link'),'class' =>'form-control']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('is_paid', __('message.is_paid'), ['class' => 'form-control-label']) }}
                                <div class="form-check">
                                    <div class="custom-control custom-radio d-inline-block col-4">
                                        <label class="form-check-label" for="is_paid-free"> {{__('message.free')}} </label>
                                        {{ Form::radio('is_paid', '0', old('is_paid') || true, [ 'class' => 'form-check-input', 'id' => 'is_paid-free']) }}
                                    </div>
                                    <div class="custom-control custom-radio d-inline-block col-4">
                                        <label class="form-check-label" for="is_paid-paid"> {{__('message.paid')}} </label>
                                        {{ Form::radio('is_paid', '1', old('is_paid'), [ 'class' => 'form-check-input', 'id' => 'is_paid-paid']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6 is_paid_price">
                                {{ Form::label('price', __('message.price').' <span class="text-danger">($)*</span>',['class'=>'form-control-label'], false ) }}
                                {{ Form::number('price', old('price'), ['class' => 'form-control',  'min' => 0, 'step' => 'any', 'required', 'placeholder' => __('message.price') ]) }}
                            </div>
                        </div>
                        <hr>
                        {{ Form::submit( __('message.save'), ['class'=>'btn btn-md btn-primary float-end']) }}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
