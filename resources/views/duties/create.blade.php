@extends('layouts.master')

@section('content')
	<form method="POST" action="{{ url('duties') }}" class="pure-form">
        <h2 class="content-subhead">Dienste so übernehmen?</h2>

        <div class="wrapper">
            <fieldset><table class="pure-table">
                <thead><tr>
                    <th>Dienstanfang</th>
                    <th>Dienstende</th>
                    <th>Fahrzeug</th>
                </tr></thead>
                <tbody>
                    @foreach($duties as $duty)
                        <tr>
                            @include('duties.create.datecell', [
                                'dt' => $duty->start,
                                'name' => "{$loop->index}-start" ])
                            @include('duties.create.datecell', [
                                'dt' => $duty->end,
                                'name' => "{$loop->index}-end" ])
                            <td>
                                <select name="{{ $loop->index }}-vehicle"></select>
                            </td>
                        </tr>
                        <tr><td colspan="3">
                            <input type="text" name="{{ $loop->index }}->comment" value placeholder="Kommentar" class="duty-comment">
                        </td></tr>
                    @endforeach
                </tbody>
            </table></fieldset>

            <fieldset class="bottom-wrapper">
                blub
            </fieldset>

            <fieldset class="bottom-wrapper">
                <input type="submit" name="save" value="Alle speichern" class="pure-button primary-button">
                <a href="{{ url()->previous() }}" class="pure-button secondary-button icon-button"><i class="fa fa-reply" aria-hidden="true"></i> Zurück</a
            </fieldset>
        </div>
    </form>
@endsection
