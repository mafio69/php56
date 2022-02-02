@if(Auth::user() && Auth::user()->signature)
    <img src="{{ url('templates-src/'.Auth::user()->signature) }}" alt="Podpis" style="height: 60px;"/>
@endif
<?php /*
@if(Auth::user()->login == 'das')
    <img src="templates-src/p_01.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'kadur')
    <img src="templates-src/katarzyna_durman.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'majozw')
    <img src="templates-src/p_03.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'AnKon')
    <img src="templates-src/andrzej_konopelski.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'justynan')
    <img src="templates-src/justyna_najdzionek.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'jagodaw')
    <img src="templates-src/jagoda_wojciechowska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'aniakk')
    <img src="templates-src/anna_krawczuk.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'konradh')
    <img src="templates-src/konrad_huk.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'malwinat')
    <img src="templates-src/malwina_twardy.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'mareko')
    <img src="templates-src/marek_ogrodowicz.png" alt="Podpis" style="height:73px;"/>
@elseif(Auth::user()->login == 'msroka')
    <img src="templates-src/magdalena_sroka.png" alt="Podpis" style="height: 110px;"/>
@elseif(Auth::user()->login == 'magrze')
    <img src="templates-src/marta_grzeszczak.png" alt="Podpis" style="height: 110px;"/>
@elseif(Auth::user()->login == 'aszcza')
    <img src="templates-src/anna_szczawinska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'przem_k')
    <img src="templates-src/anna_szczawinska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'iwrad')
    <img src="templates-src/iwona_radwanska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'agbog')
    <img src="templates-src/agata_bogacz.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'pamaj')
    <img src="templates-src/paulina_majerska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'nawod')
    <img src="templates-src/natalia_wodecka.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'ewmaj')
    <img src="templates-src/ewelina_pawlik.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'pakoz')
    <img src="templates-src/patrycja_koziolek.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'juszu')
    <img src="templates-src/justyna_szumiato.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'amendel')
    <img src="templates-src/artur_mendel.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'jkalinowska')
    <img src="templates-src/justyna_kalinowska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'aglek')
    <img src="templates-src/agnieszka_lektarska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'emwojew')
    <img src="templates-src/emilia_wojewodzka.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'makula')
    <img src="templates-src/maja_kula.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'madys')
    <img src="templates-src/martyna_dysko.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'mapys')
    <img src="templates-src/malgorzata_pysiak.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'jjozwa')
    <img src="templates-src/justyna_jozwa.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'jamil')
    <img src="templates-src/jakub_milosek.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'agapa')
    <img src="templates-src/agata_paw.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'pausar')
    <img src="templates-src/paulina_sarnowska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'karoku')
    <img src="templates-src/karolina_kupczak.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'kaziel')
    <img src="templates-src/katarzyna_zielke.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'natkin')
    <img src="templates-src/natalia_kinkel.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'dkopacka')
    <img src="templates-src/dominika_kopacka.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'ebula')
    <img src="templates-src/ewelina_bula.png" alt="Podpis" style="height: 53px;"/>
@elseif(Auth::user()->login == 'domkow')
    <img src="templates-src/dominika_kowalska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'anhos')
    <img src="templates-src/aneta_hostynska.png" alt="Podpis" style="height: 73px;"/>
@elseif(Auth::user()->login == 'mlewandowska')
    <img src="templates-src/marta_lewandowska.png" alt="Podpis" style="height: 73px;"/>
@endif

*/
?>
