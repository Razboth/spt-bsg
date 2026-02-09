<?php

//Route::get('/migrate/position', function () {
//    $data = [
//        "MGR KREDIT KOMERSIAL",
//        "AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 2",
//        "MGR KREDIT KOMERSIAL",
//        "AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "Jr. AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 1",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "Jr. RELATIONSHIP OFFICER",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "PEMASAR DANA",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "RELATIONSHIP OFFICER 1",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "Jr. RELATIONSHIP OFFICER",
//        "AO KREDIT",
//        "PEMASAR DANA",
//        "AST. MGR KREDIT",
//        "MGR KREDIT",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 2",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "PEMASAR DANA",
//        "MGR KREDIT KOMERSIAL",
//        "AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 1",
//        "MGR KREDIT",
//        "AO KREDIT KOMERSIAL",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 1",
//        "MGR KREDIT KOMERSIAL",
//        "AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 1",
//        "MGR KREDIT KOMERSIAL",
//        "AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RO FUNDING 2",
//        "MGR KREDIT KOMERSIAL",
//        "Jr. AO KREDIT KOMERSIAL",
//        "MGR KREDIT KONSUMER",
//        "AO KREDIT KONSUMER",
//        "MGR DANA",
//        "RELATIONSHIP OFFICER 1",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "Jr. RELATIONSHIP OFFICER",
//        "AST. MGR BISNIS",
//        "AO KREDIT",
//        "PEMASAR DANA",
//    ];
//
//
//    $lastOrder = 35;
//    foreach ($data as $k => $position){
//        $checkMaster = \App\Models\Setting\AppMasterData::where('app_master_category_code', 'PMP')
//            ->where('name', $position)
//            ->first();
//
//        if(empty($checkMaster)){
//            $lastOrder++;
//            \App\Models\Setting\AppMasterData::create([
//                'name' => $position,
//                'app_master_category_code' => 'PMP',
//                'code' => 'PMP' . str_pad($lastOrder, 3, '0', STR_PAD_LEFT),
//                'order' => $lastOrder,
//                'status' => 't',
//            ]);
//        }
//    }
//});
//
//Route::get('/migrate/branch', function () {
//    $data = [
//         "CABANG TAHUNA",
//        "CABANG SIAU",
//        "CAPEM LIRUNG",
//        "CAPEM TAGULANDANG",
//        "CABANG MELONGUANE",
//        "CAPEM TAMAKO",
//        "CAPEM BEO",
//        "CABANG BITUNG",
//        "CABANG KAWANGKOAN",
//        "CABANG TONDANO",
//        "CABANG TOMOHON",
//        "CABANG AIRMADIDI",
//        "CAPEM LIKUPANG",
//        "CAPEM MANEMBO-NEMBO",
//        "CABANG UTAMA",
//        "CABANG CALACA",
//        "CAPEM TUMINTING",
//        "CAPEM PAAL DUA",
//        "CAPEM BAHU",
//        "CAPEM RANOTANA",
//        "CAPEM SMS SAM RATULANGI",
//        "CABANG RATAHAN",
//        "CAPEM LANGOWAN",
//        "CABANG KOTAMOBAGU",
//        "CABANG AMURANG",
//        "CABANG BOROKO",
//        "CABANG TUTUYAN",
//        "CABANG MOLIBAGU",
//        "CABANG LOLAK",
//        "CAPEM MOTOLING",
//        "CAPEM MOPUYA",
//        "CAPEM MODOINDING"
//    ];
//
//    $lastOrder = 53;
//    foreach ($data as $k => $branch) {
//        $checkMaster = \App\Models\Setting\AppMasterData::where('app_master_category_code', 'PMUK')
//            ->where('name', $branch)
//            ->first();
//
//        if (empty($checkMaster)) {
//            $lastOrder++;
//            \App\Models\Setting\AppMasterData::create([
//                'name' => $branch,
//                'app_master_category_code' => 'PMUK',
//                'code' => 'PMUK' . str_pad($lastOrder, 3, '0', STR_PAD_LEFT),
//                'order' => $lastOrder,
//                'status' => 't',
//            ]);
//        }
//    }
//});
//
//Route::get('/migrate/sales', function () {
//    $data = "
//    RISY J. TUMIWA	7811.424	PINCAB KCU	CABANG UTAMA
//TOMY GOBEL	7509.417	PINCAB	CABANG GORONTALO
//FERNANDO F. MAMAHIT	7702.462	PINCAB	CABANG TAHUNA
//JAMES J. LUMINTANG	7506.411	PINCAB	CABANG BITUNG
//THREISYE NAJOAN	8501.839	PINCAB	CABANG KAWANGKOAN
//HASAN HAMID	7510.406	PINCAB	CABANG LIMBOTO
//LAURENSIA D. SUMEYSEY	7702.445	PINCAB	CABANG TONDANO
//LIDYA T.N. DONDOKAMBEY	7811.1758	Pj. PINCAB	CABANG TOMOHON
//ZULHIJAS A. RASYID	8309.747	Pj. PINCAB	CABANG MARISA
//BROERY JACOBUS	8009.504	PINCAB	CABANG CALACA
//MEIFIE M RUNTULALO	6905.334	PINCAB	CABANG AMURANG
//CHERYL L. HUMIANG	8702.912	PINCAB	CABANG SIAU
//CHRISTIN Y. PELLE	8812.925	PINCAPEM	CAPEM LIRUNG
//ENDAH BAHNAN	7902.437	Pj. PINCAB	CABANG TILAMUTA
//JEANETTE V. S RAMPI	8101.499	PINCAB	CABANG JAKARTA
//MAYKEL R. RANTUNG	7404.410	PINCAB	CABANG AIRMADIDI
//SRI WAHYUNI WANTOGIA	7202.396	PINCAB	CABANG SUWAWA
//KAMALUDIN ZEES	7104.302	PINCAB	CABANG KWANDANG
//STANLY MANANGKA	7909.659	PINCAB	CABANG BOROKO
//LISDAWATY SIMATUPANG	7612.492	PINCAPEM	CAPEM KELAPA GADING
//JEANE Y. ARINA	7906.501	PINCAB	CABANG RATAHAN
//NOEBEL TAMBAJONG	8011.615	PINCAB	CABANG SURABAYA
//REINTJE A. KEINTJEM	7101.985	PINCAPEM	CAPEM CEMPAKA PUTIH
//ANDREW WALUKOW	7910.648	PINCAPEM	CAPEM MANGGA DUA
//HERU NOERTJAHJO	6904.1001	PINCAB	CABANG MALANG
//ASWIN PAPUTUNGAN	8206.677	PINCAB	CABANG TUTUYAN
//HENDRA J. MONOARFA	8408.1050	PJ. PINCAPEM	CAPEM POPAYATO
//YAN K. PUSPA	8501.657	PINCAPEM	CAPEM PASAR SENTRAL
//JERRY TUUK	8101.467	PINCAB	CABANG MOLIBAGU
//MIRANDA E. THOMAS	7701.427	PINCAB	CABANG LOLAK
//VEMY BOGAR	7302.362	PINCAPEM	CAPEM TAGULANDANG
//JEANY N. DUMONDOR	8306.695	PINCAPEM	CAPEM TUMINTING
//STEVEN M. KAWENGIAN	7410.416	PINCAPEM	CAPEM LIKUPANG
//IVONE D. GONI	7908.412	PINCAPEM	CAPEM PAAL DUA
//TESSY F. GOBEL	9102.1036	PINCAPEM	CAPEM PAGUAT
//JOICE S MONIAGA	7401.404	PINCAPEM	CAPEM MOTOLING
//STEVIE P. MARINGKA	8808.862	PINCAB	CABANG MELONGUANE
//LUCKY PUTONG	7607.490	PINCAPEM	CAPEM MANEMBO-NEMBO
//KEMAL SUKARTA	8103.619	PINCAPEM	CAPEM RANDANGAN
//SUUD ALHASNI	8210.625	PINCAPEM	CAPEM TOLANGOHULA
//NOLDY A. MENDE	7511.638	PJ. PINCAPEM	CAPEM BAHU
//CLIEF A. MAMUSUNG	8306.858	PINCAPEM	CAPEM RANOTANA
//SERPI LONGDONG	8509.880	PINCAPEM	CAPEM TAMAKO
//ILVANA STEVANI	8008.662	PINCAPEM	CAPEM PAGUYAMAN
//CLIFFORD RONDONUWU	7509.488	PINCAPEM	CAPEM SMS SAM RATULANGI
//TONNY M. RUMIMPER	7903.474	PINCAPEM	CAPEM BEO
//ENDRO I. TOREH	8703.702	PINCAPEM	CAPEM SMS TELAGA
//NOVY V. LUMUNON	6912.344	PINCAPEM	CAPEM MOPUYA
//JEFTA A. I. KALIGIS	7108.313	PINCAPEM	CAPEM MODOINDING
//    ";
//
//// 1) Pecah ke baris
//    $rows = array_filter(array_map('trim', explode("\n", $data)));
//
//// 2) Konversi setiap baris menjadi associative array [nama, npp, jabatan, unit_kerja]
//    $records = array_map(function ($row) {
//        $cols = array_map('trim', explode("\t", $row));
//        return [
//            'nama' => $cols[0] ?? '',
//            'npp' => $cols[1] == '0000.0'
//                ? str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT)
//                : ($cols[1] ?? ''),
//            'jabatan' => $cols[2] ?? '',
//            'unit_kerja' => $cols[3] ?? '',
//        ];
//    }, $rows);
//
//    foreach ($records as $k => $record) {
//        $getPositionId = \App\Models\Setting\AppMasterData::where('app_master_category_code', 'PMP')
//            ->where('name', $record['jabatan'])
//            ->first();
//
//        if (empty($getPositionId)) {
//            echo "Position not found for: " . $record['jabatan'] . "\n";
//        }
//
//        $getBranchId = \App\Models\Setting\AppMasterData::where('app_master_category_code', 'PMUK')
//            ->where('name', $record['unit_kerja'])
//            ->first();
//
//        if (empty($getBranchId)) {
//            echo "Branch not found for: " . $record['unit_kerja'] . "\n";
//        }
//
//        $agent = \App\Models\PSA\Agent::create([
//            'name' => $record['nama'],
//            'employee_number' => $record['npp'],
//            'position_id' => $getPositionId->id ?? null,
//            'unit_id' => $getBranchId->id ?? null,
//            'rank_id' => '907b4487-500a-4753-b552-b5c1db611a7e',
//            'type_id' => 'c59215e7-bd7a-48cd-b0d5-15eaa435dc1e',
//            'status_id' => '38f6cd0b-bc8c-4952-98cf-137e7f4b3f73',
//        ]);
//
//        $user = \App\Models\Setting\User::create([
//            'name' => $record['nama'],
//            'username' => $record['npp'],
//            'email' => $record['npp'] . '@example.com',
//            'password' => bcrypt('password'),
//            'model_id' => $agent->id,
//            'status' => 't',
//            'role_id' => '13'
//        ]);
//
//        $user->assignRole('13');
//    }
//});
//
//Route::get('/migrate/check-agent', function (){
//    $agents = [
//        "RUDINI KEMBUAN",
//        "HIKMATULLAH MOKOAGOW",
//        "MURSALIN SAGIO",
//        "CHIQUITA MASENGI",
//        "SORAYA P. KOAGOW",
//        "ERICK M. ZOUGIRA",
//        "ARNOLD G. MAHIPE",
//        "JULVAN UMBOH",
//        "OKTAVIAN KALIGIS",
//        "RICHARD A. WAROUW",
//        "APRILIANI PULUKADANG",
//        "RONALD MAWEY",
//        "ANDRE WATTIE",
//        "WIDYA P. PONTOH",
//        "ELVIRA MELIANA DATUELA",
//        "HISAM AMBARAK",
//        "DAVIS K. TAMPI",
//        "WILLY J. MANGULU",
//        "MARLEN L. KONTU",
//        "TESAR DAMOPOLII",
//        "FALDY F. ALUY",
//        "DESANTI F. DONGGALA",
//        "RATNA D. GINOGA",
//        "IWAN HEINSKEIN MANGGOPA",
//        "SRI PUTRI. H. KIAY DEMAK",
//        "ZHENDY PAPUTUNGAN",
//        "CHANDRA SALIBANA",
//        "JUNAMEY LUMENTUT",
//        "BRYAN W F RAHIM",
//        "SHARON G PARUNTU",
//        "KANIA KAPAHANG",
//        "RYAN S. KOROMPOT",
//        "RIFKI APRIANTO GINOGA",
//        "KAROLIN K. POLII",
//        "GIOVANI X. UMBOH",
//            "DAVID A. WINERUNGAN",
//        "MEILISA RUMETOR",
//        "HUTRI R. B. LALOAN",
//        "CHRISTOPHER D. LUASUNAUNG",
//        "EDWIN RUDIANTO",
//        "WHITNEY KENNY REY PANGKEY",
//        "NIXON R. S. PURUKAN",
//        "NIKHYTA H. F. TENGES",
//        "RAYDEL PANEKENAN",
//        "GABRIELLA MARETTA",
//        "ZOYA J. SUMARAUW",
//        "NATALIA C. MANTIRI",
//        "JOANA E WAWOLUMAYA",
//        "ANASTASIA F. RUMAGIT",
//        "DEYSI ANTU",
//        "KAREN ANGELA BATARA TUPPANG",
//        "WARANEY W. PURUKAN",
//        "BRIANT D. KUMENDONG",
//        "GABRIELA VIRGINIA BAWOLE",
//        "GIANNY M. DANAKUSUMAH",
//        "SJEREN ADELEIDA SAROINSONG",
//        "MARCELITA P.M. LIONO",
//        "ALICE M. DEBORAH",
//        "CHRISTIAN Ch. ROMPAS",
//        "MAYNARD LUDWIG SENDUK",
//        "AMELIA C. DUSAUW",
//        "NORMA YUNITA KADADIA",
//        "IREINE Y. RUMONDOR",
//        "YULITA A. RUMONDOR",
//        "ERLANGGA C. J. POLUAN",
//        "GABRIELA C. RORING",
//        "INRI A. E. RENDE",
//        "WINSTON C. TARORE",
//        "ALVIANO M. MAARIAL",
//        "WIDDYA TANGKULUNG",
//            "HANA S. WURANGIAN",
//        "CHRISTO SALAWATI",
//        "MEILA O. TUJU",
//        "PAULA I. SIGARLAKI",
//        "ROZILDA M. MARAMIS",
//        "FALINCIA F LASUT",
//        "GARRY P. RUMENGAN",
//        "JELLEN L. RAWUNG",
//        "ANDIKA D. KAPOJOS",
//        "OLIVIA AGUW",
//        "IGNATIUS B. AROR",
//        "JIMMY J. LOLOWANG",
//        "YEHEZKIEL M M RAWUNG",
//        "ALVIAN TURANG",
//        "MIRANDA WAKARI",
//        "JESIKA NGANTUNG",
//        "SISILIA I. KUMONTOY",
//        "NADYA G. KAROUWAN",
//        "IVAN G. BANGUN",
//        "DARYL J. RASUH",
//        "HELVIE C. RUGIAN",
//        "RATNA L. POLII",
//        "RIZKY R. KALIGIS",
//        "KEITH KALONTA",
//        "FARIZ A. KARINDA",
//        "INDIRA A LANTEMONA",
//        "DONALD R. PARANGKA",
//        "CYNTHIA C. RUMENGAN",
//        "WINDY AROR",
//        "GABRIELLA M. LALAMENTIK",
//        "EDEN BILL KENNY LAKATUA",
//        "MARCO MONGKAU",
//        "ANDRE PALIT",
//        "DONAL JHONSON DOTULONG",
//            "ROYKE OMPI",
//        "JUAN M. WALUKOW",
//        "MICK W. PAPARANG",
//        "DIAN L. TAMPI",
//        "MAYER MAKUTIKA",
//        "ALFA TAWERA",
//        "DESTINIA LANGI",
//        "EDWAR LOONG",
//        "RIVI REYNALDI MALAGANI",
//        "FRANSISCO L. THOMAS",
//        "MONDY SUMARAUW",
//        "VALDY WOWOR",
//        "CHRISTINE LOLOWANG",
//        "MARIA M. TURANG",
//        "SANDHI C. KAKASIH",
//        "HOC GETA",
//        "ESTER TINGGINEHE",
//        "NURIATI MANANGKA",
//        "NORMA SALIBANA",
//        "ROYNALD KATIANDAGHO",
//        "NURWINDA A. MAGULILI",
//        "IAN FABIO LALA",
//        "NOVRY L. JACOBUS",
//        "DEBI POTOBODA",
//        "LIDYA DAROEL",
//        "STEVA G. RATTU",
//        "HORNES SURUPANGGIL",
//        "EVA DUARTE MUNTU",
//        "ROBERT R. MANGERONGKONDA",
//        "CHRYSTIAN E. J. MANDIRI",
//        "PINGKAN T. KAUNANG",
//        "ESTER BENTIAN",
//        "REINER S. SINADIA",
//        "JULIO JAKOBUS C. LOPULALAN",
//    ];
//
//    foreach ($agents as $k => $agent){
//        $checkAgent = \App\Models\PSA\Agent::where('name', $agent)->first();
//        if(empty($checkAgent)){
//            echo "Agent not found: " . $agent . "<br>";
//        }
//    }
//})



?>