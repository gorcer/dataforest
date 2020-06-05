@php( $fields = array_keys($data[0]))

<table class="table">
    <thead>
        <tr>
            @foreach($fields as $field)
                <th scope="col">{{$field}}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($data as $item)
            <tr>
                @foreach($item as $value)
                    @if(is_array($value))
                        <td> {!!json_encode($value, JSON_PRETTY_PRINT)!!} </td>
                    @else
                        <td>{{$value}}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach

    </tbody>
</table>