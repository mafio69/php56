define({ "api": [  {    "type": "post",    "url": "ea/login",    "title": "Logowanie się do usługi.",    "name": "Login",    "group": "Autoryzacja",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "login",            "description": "<p>Login do konta</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "password",            "description": "<p>Hasło do konta</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Zakończone powodzeniem",          "content": "HTTP 200\n{\n  \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2xvZ2luIiwiaWF0IjoiMTU3MTQwMzUzNiIsImV4cCI6IjE1NzE0MDcxMzYiLCJuYmYiOiIxNTcxNDAzNTM2IiwianRpIjoiMWE0N2EzYTMzNDM4YjA3ZWI5MmRkMDcwODk1ZWE1ZDUifQ.ZmVjYjlhZjk4M2FlYzU5NmZhMzA2MWIyNGI4YjExYmU0ZWUyMTNlYzM1NDcwNzVhNWE2ZjRjMTE3NDJlZjMzOA\"\n}",          "type": "json"        }      ]    },    "error": {      "examples": [        {          "title": "Nieprawidłowy login lub hasło",          "content": "HTTP 401\n{\n  \"error\": \"invalid_credentials\"\n}",          "type": "json"        },        {          "title": "Brak przesłanego api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_required\"\n}",          "type": "json"        },        {          "title": "Błędny api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_invalid\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/AuthenticateController.php",    "groupTitle": "Autoryzacja"  },  {    "type": "post",    "url": "ea/refresh-token",    "title": "Odświeżenie tokenu.",    "name": "Od_wie_enie_tokenu",    "group": "Autoryzacja",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Zakończone powodzeniem",          "content": "HTTP 200\n{\n  \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2xvZ2luIiwiaWF0IjoiMTU3MTQwMzUzNiIsImV4cCI6IjE1NzE0MDcxMzYiLCJuYmYiOiIxNTcxNDAzNTM2IiwianRpIjoiMWE0N2EzYTMzNDM4YjA3ZWI5MmRkMDcwODk1ZWE1ZDUifQ.ZmVjYjlhZjk4M2FlYzU5NmZhMzA2MWIyNGI4YjExYmU0ZWUyMTNlYzM1NDcwNzVhNWE2ZjRjMTE3NDJlZjMzOA\"\n}",          "type": "json"        }      ]    },    "error": {      "examples": [        {          "title": "Brakujący token",          "content": "HTTP 400\n{\n  \"error\": \"token_not_provided\"\n}",          "type": "json"        },        {          "title": "Wygasły token",          "content": "HTTP 401\n{\n  \"error\": \"token_invalid\"\n}",          "type": "json"        },        {          "title": "Brak przesłanego api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_required\"\n}",          "type": "json"        },        {          "title": "Błędny api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_invalid\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/AuthenticateController.php",    "groupTitle": "Autoryzacja"  },  {    "type": "post",    "url": "ea/type-incident-list",    "title": "Rodzaje zdarzeń",    "name": "Lista_rodzai_zdarze_",    "group": "Metody",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Success-Response:",          "content": "    HTTP/1.1 200 OK\n{\n    \"typeIncidents\": [\n        {\n            \"id\": 1,\n            \"name\": \"Kolizja z innym pojazdem\"\n        },\n        {\n            \"id\": 2,\n            \"name\": \"Potrącenie pieszego/ innego uczestnika ruchu\"\n        },\n        {\n            \"id\": 3,\n            \"name\": \"Kolizja ze zwierzęciem\"\n        }\n    ],\n    \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjMsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2VhXC90eXBlLWluY2lkZW50LWxpc3QiLCJpYXQiOiIxNTc0MzIxNDE2IiwiZXhwI\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/ApiEaController.php",    "groupTitle": "Metody"  },  {    "type": "post",    "url": "ea/vehicle",    "title": "Wyszukanie pojazdu w bazie.",    "name": "Wyszukaj_pojazd",    "group": "Metody",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "registration",            "description": "<p>Numer rejestracyjny pojazdy</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "vin",            "description": "<p>Vin pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "contract_number",            "description": "<p>Numer umowy pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Success-Response:",          "content": "HTTP/1.1 200 OK\n {\n     \"vehicles\":[\n         {\n             \"vehicle_id\":220,\n             \"vehicle_type\":\"1\",\n             \"registration\":\"WB7843H\",\n             \"brand\":\"Audi\",\n             \"model\":\"A6\",\n             \"vin\":\"WAUZZZ4G5EN041271\",\n             \"year_production\":2013,\n             \"owner\":\"VW Leasing\",\n             \"sales_program\":IDX,\n             \"contract_number\":201/2019,\n             \"end_leasing\":\"2019-12-13\",\n             \"insurance_company\":\"PZU S.A.\",\n             \"insurance_expire_date\":\"2015-11-08\",\n             \"policy_number\":\"EN04123SA12/12\"\n         }\n     ],\n     \"token\":\"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL3ZlaGljbGUiLCJpYXQiOiIxNTcxNDAzOTA5IiwiZXhwIjoiMTU3MTQwNzkwNyIsIm5iZiI6IjE1NzE0MDQzMDciLCJqdGkiOiIyYjkwMjYwNWMyZWEwMjM4ZTU0YjY2NDYzOWUyMGNmNyJ9.MmZjNzZmN2E5NDkxNjk5YTQ4ZTBmMTY5ZmI0NzZkNTRiOGViZjQ1ZmM4ODkwZGZjYzMzYzFkNTQxNjJmYjRkYg\"\n }",          "type": "json"        }      ]    },    "error": {      "examples": [        {          "title": "Brakujący token",          "content": "HTTP 400\n{\n  \"error\": \"token_not_provided\"\n}",          "type": "json"        },        {          "title": "Wygasły token",          "content": "HTTP 401\n{\n  \"error\": \"token_invalid\"\n}",          "type": "json"        },        {          "title": "Brak przesłanego api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_required\"\n}",          "type": "json"        },        {          "title": "Błędny api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_invalid\"\n}",          "type": "json"        },        {          "title": "Brak przesłanych parametrów",          "content": "HTTP 400\n{\n   \"error\": \"data_missed\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/ApiEaController.php",    "groupTitle": "Metody"  },  {    "type": "post",    "url": "ea/car-workshops",    "title": "Wyszukanie dopasowanych warsztatów.",    "name": "Wyszukaj_warsztat",    "group": "Metody",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "sales_program",            "description": "<p>Kod programu sprzedażowego</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "city",            "description": "<p>Miejscowość w której znajduje się serwis</p>"          },          {            "group": "Parameter",            "type": "Decimal",            "optional": true,            "field": "lat",            "description": "<p>Współrzędna latitude położenia serwisu</p>"          },          {            "group": "Parameter",            "type": "Decimal",            "optional": true,            "field": "lng",            "description": "<p>Współrzędna longitude położenia serwisu</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "radius",            "description": "<p>Obszar [km] w promieniu którego znajduje się serwis</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Success-Response:",          "content": "  HTTP/1.1 200 OK\n{\n    \"workshops\": [\n        {\n            \"id\": 4137,\n            \"name\": \" \\\"Auto-Błysk\\\" Andrzej Baranowski \",\n            \"nip\": \"\",\n            \"street\": \"Młynarska 10\",\n            \"code\": \"84-351\",\n            \"city\": \"Nowa Wieś Lęborska\",\n            \"email\": \"\",\n            \"phone\": \"\",\n            \"contact_people\": \"\",\n            \"lat\": 54.562988,\n            \"lng\": 17.735567,\n            \"open_time\": \"08:00:00\",\n            \"close_time\": \"18:00:00\",\n            \"available_range\": [\n                \"blacharsko-lakierniczy (ciężarowe)\",\n                \"blacharsko-lakierniczy (dostawcze do 3,5 t)\",\n                \"blacharsko-lakierniczy (osobowe)\",\n                \"diagnostyka okręgowa (ciężarowe)\",\n                \"diagnostyka okręgowa (osobowe)\",\n                \"diagnostyka podstawowa (ciężarowe)\",\n                \"diagnostyka podstawowa (osobowe)\",\n                \"mechaniczny (ciężarowe)\",\n                \"mechaniczny (osobowe)\",\n                \"szyby\",\n                \"wulkanizacyjny (ciężarowe)\",\n                \"wulkanizacyjny (osobowe)\"\n            ],\n            \"available_brands\": [\n                \"Audi\",\n                \"BMW\",\n                \"Wielton\",\n                \"Still\"\n            ],\n            \"plan_groups\": [\n                {\n                    \"name\": \"PN1\",\n                    \"conditional_list\": false\n                },\n                {\n                    \"name\": \"PN2\",\n                    \"conditional_list\": true\n                }\n            ],\n            \"address\": \"84-351 Nowa Wieś Lęborska, Młynarska 10\",\n            \"company\": {\n                \"name\": \" \\\"Auto-Błysk\\\" Andrzej Baranowski \",\n                \"street\": \"Młynarska 10 \",\n                \"code\": \"84-351\",\n                \"city\": \"Nowa Wieś Lęborska\",\n                \"nip\": \"\",\n                \"krs\": \"\",\n                \"regon\": \"\",\n                \"www\": \"\",\n                \"email\": \"\",\n                \"phone\": \"\",\n                \"account_nr\": \"\"\n            }\n        },\n        {\n            \"id\": 4312,\n            \"name\": \"MAGMAR Sp z o.o. \",\n            \"nip\": null,\n            \"street\": \"KOBYLOGÓRSKA 98\",\n            \"code\": \"66-400\",\n            \"city\": \"GORZÓW WIELKOPOLSKI \",\n            \"email\": \"\",\n            \"phone\": \"\",\n            \"contact_people\": null,\n            \"lat\": 0,\n            \"lng\": 0,\n            \"open_time\": null,\n            \"close_time\": null,\n            \"available_range\": [],\n            \"available_brands\": [],\n            \"plan_groups\": [\n                {\n                    \"name\": \"PN1\",\n                    \"conditional_list\": false\n                },\n                {\n                    \"name\": \"PN2\",\n                    \"conditional_list\": true\n                }\n            ],\n            \"address\": \"66-400 GORZÓW WIELKOPOLSKI , KOBYLOGÓRSKA 98\",\n            \"company\": {\n                \"name\": \"MAGMAR Sp z o.o. \",\n                \"street\": \"KOBYLOGÓRSKA 98\",\n                \"code\": \"66-400\",\n                \"city\": \"GORZÓW WIELKOPOLSKI \",\n                \"nip\": \"5993163878\",\n                \"krs\": \"\",\n                \"regon\": \"\",\n                \"www\": \"\",\n                \"email\": \"\",\n                \"phone\": \"\",\n                \"account_nr\": \"\"\n            }\n        },\n        {\n            \"id\": 4992,\n            \"name\": \" \\\"Auto-Błysk\\\" Andrzej Baranowski \",\n            \"nip\": \"\",\n            \"street\": \"Młynarska 10 \",\n            \"code\": \"84-351\",\n            \"city\": \"Nowa Wieś Lęborska\",\n            \"email\": \"\",\n            \"phone\": \"\",\n            \"contact_people\": \"\",\n            \"lat\": 54.562988,\n            \"lng\": 17.735567,\n            \"open_time\": \"00:00:00\",\n            \"close_time\": \"00:00:00\",\n            \"available_range\": [\n                \"blacharsko-lakierniczy (ciężarowe)\",\n                \"blacharsko-lakierniczy (dostawcze do 3,5 t)\",\n                \"blacharsko-lakierniczy (osobowe)\",\n                \"diagnostyka okręgowa (ciężarowe)\",\n                \"diagnostyka okręgowa (osobowe)\",\n                \"diagnostyka podstawowa (ciężarowe)\",\n                \"diagnostyka podstawowa (osobowe)\",\n                \"mechaniczny (ciężarowe)\",\n                \"mechaniczny (osobowe)\",\n                \"wulkanizacyjny (ciężarowe)\",\n                \"wulkanizacyjny (osobowe)\"\n            ],\n            \"available_brands\": [\n                \"Audi\",\n                \"Hyundai\"\n            ],\n            \"plan_groups\": [\n                {\n                    \"name\": \"PN1\",\n                    \"conditional_list\": false\n                },\n                {\n                    \"name\": \"PN2\",\n                    \"conditional_list\": true\n                }\n            ],\n            \"address\": \"84-351 Nowa Wieś Lęborska, Młynarska 10 \",\n            \"company\": {\n                \"name\": \" \\\"Auto-Błysk\\\" Andrzej Baranowski \",\n                \"street\": \"Młynarska 10 \",\n                \"code\": \"84-351\",\n                \"city\": \"Nowa Wieś Lęborska\",\n                \"nip\": \"\",\n                \"krs\": \"\",\n                \"regon\": \"\",\n                \"www\": \"\",\n                \"email\": \"\",\n                \"phone\": \"\",\n                \"account_nr\": \"\"\n            }\n        }\n    ],\n    \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2Nhci13b3Jrc2hvcHMiLCJpYXQiOiIxNTcyNDMxMDA4IiwiZXhwIjoiMTU3MjQzNzc4MSIsIm5iZiI6IjE1NzI0MzQxODEiLCJqdGkiOiI4NzZkYzdlNmRjY2U4YmExNzBiODk4NDc2Mzg0NzkyOSJ9.ZDY0Y2IwYWU3YzdjMTI0YjFjMDI5ZWUxMTY5ZWMzZDVlYTFmYzQ4ZGEyNjQyMzQyYjM4NWVkYTVmNjZlZGUxZA\"\n}",          "type": "json"        }      ]    },    "error": {      "examples": [        {          "title": "Brakujący token",          "content": "HTTP 400\n{\n  \"error\": \"token_not_provided\"\n}",          "type": "json"        },        {          "title": "Wygasły token",          "content": "HTTP 401\n{\n  \"error\": \"token_invalid\"\n}",          "type": "json"        },        {          "title": "Brak przesłanego api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_required\"\n}",          "type": "json"        },        {          "title": "Błędny api key",          "content": "HTTP 400\n{\n  \"error\": \"api_key_invalid\"\n}",          "type": "json"        },        {          "title": "Brak przesłanych parametrów",          "content": "HTTP 400\n{\n   \"error\": \"data_missed\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/ApiEaController.php",    "groupTitle": "Metody"  },  {    "type": "post",    "url": "ea/update-injury",    "title": "Aktualizacja szkody",    "name": "Zaktualizuj_dane_szkody",    "group": "Metody",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "case_number",            "description": "<p>Identyfikator szkody EA</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_number",            "description": "<p>numer szkody</p>"          }        ]      }    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/ApiEaController.php",    "groupTitle": "Metody"  },  {    "type": "post",    "url": "ea/register-injury",    "title": "Rejestracja szkody w DLS",    "name": "Zarejestruj_szkod_",    "group": "Metody",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "Authorization",            "description": "<p>Bearer: Token sesji</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "api_key",            "description": "<p>Klucz api do modułu</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": false,            "field": "vehicle_id",            "description": "<p>Id pojazdu zwracane z API</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "vehicle_type",            "description": "<p>Typ pojazdu zwracane z API</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "sales_program",            "description": "<p>Program sprzedaży zwracane z API</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "workshop_id",            "description": "<p>id warsztatu zwracane z API</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_vin",            "description": "<p>vin pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_registration",            "description": "<p>nr rejestracyjny pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_brand",            "description": "<p>marka pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_model",            "description": "<p>model pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_engine_capacity",            "description": "<p>pojemność silnika</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_year_production",            "description": "<p>rok produkcji</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_first_registration",            "description": "<p>data pierwszej rejestracja</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "vehicle_mileage",            "description": "<p>przebieg</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "owner_name",            "description": "<p>nawa właściciela</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "client_name",            "description": "<p>nazwa klienta</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "contract_number",            "description": "<p>nr umowy</p>"          },          {            "group": "Parameter",            "type": "Date",            "optional": true,            "field": "contract_end_leasing",            "description": "<p>data końca leasingu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "contract_status",            "description": "<p>status umowy</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "insurance_company_name",            "description": "<p>polisa - nazwa ZU</p>"          },          {            "group": "Parameter",            "type": "Date",            "optional": true,            "field": "insurance_expire_date",            "description": "<p>polisa - data ważności</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "insurance_policy_number",            "description": "<p>polisa - nr polisy</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "insurance_amount",            "description": "<p>polisa - suma ubezpieczenia</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "insurance_own_contribution",            "description": "<p>polisa - udział własny</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "insurance_net_gross",            "description": "<p>polisa netto/brutto: 1-netto, 2-brutto, 3-50% VAT</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "insurance_assistance",            "description": "<p>polisa assistance: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "insurance_assistance_name",            "description": "<p>polisa - nazwa pakietu assistance</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "driver_name",            "description": "<p>kierowca - imię</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "driver_surname",            "description": "<p>kierowca - nazwisko</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "driver_phone",            "description": "<p>kierowca - telefon</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "driver_email",            "description": "<p>kierowca - email</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "driver_city",            "description": "<p>kierowca - miasto zamieszkania</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "claimant_name",            "description": "<p>zgłaszający - imię</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "claimant_surname",            "description": "<p>zgłaszający - nazwisko</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "claimant_phone",            "description": "<p>zgłaszający - telefon</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "claimant_email",            "description": "<p>zgłaszający - imię</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "claimant_city",            "description": "<p>zgłaszający - miasto zamieszkania</p>"          },          {            "group": "Parameter",            "type": "Date",            "optional": true,            "field": "injury_event_date",            "description": "<p>szkoda - data zdarzenia</p>"          },          {            "group": "Parameter",            "type": "Time",            "optional": true,            "field": "injury_event_time",            "description": "<p>szkoda - godzina zdarzenia</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_event_city",            "description": "<p>szkoda - miejsce zdarzenia: miasto</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_event_street",            "description": "<p>szkoda - miejsce zdarzenia: ulica</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_type_incident_id",            "description": "<p>id rodzaju zdarzenia z API</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_event_description",            "description": "<p>opis okoliczności szkody</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_damage_description",            "description": "<p>opis uszkodzeń</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_current_location",            "description": "<p>aktualna pozycja pojazdu</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_reported_insurance_company",            "description": "<p>szkoda zgłoszona do ZU: 1:tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_type",            "description": "<p>typ szkody</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_number",            "description": "<p>numer szkody</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_insurance_company",            "description": "<p>zakład ubezpieczeń</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_police_notified",            "description": "<p>policja - zawiadomiono: -1:nie ustalono, 0:nie, 1:tak</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_police_number",            "description": "<p>policja - nr zgłoszenia</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_police_unit",            "description": "<p>policja - jednostka</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "injury_police_contact",            "description": "<p>policja - kontakt</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_statement",            "description": "<p>spisano oświadczenia: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_taken_registration",            "description": "<p>zabrano dowód rejestracyjny: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_towing",            "description": "<p>wymaga holowanie: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_replacement_vehicle",            "description": "<p>wymagane auto zastępcze: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "Integer",            "optional": true,            "field": "injury_vehicle_in_service",            "description": "<p>samochód znajduje się w warsztacie: 1-tak, 0-nie</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "case_number",            "description": "<p>numer sprawy</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "Success-Response:",          "content": "HTTP/1.1 200 OK\n {\n      \"token\": \"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjMsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2VhXC9yZWdpc3Rlci1pbmp1cnkiLCJpYXQiOiIxNTc0MTYwNzk2IiwiZXhwIjoiMTU3NDE2NTAxOCIsIm5iZiI6IjE1NzQxNjE0MTgiLCJqdGkiOiI2Mjk2ZDA0MTQ2ZTQ3YTA0YTk0ZTQ2M2U3MDE4OTE0NyJ9.YzFlZmQyZmZiZGVhODQ0MTYzZDkwZjc4ZDM0MTdiOWZiY2JkNDg5OWEzZGQxNWViYWQ2YjZjOWI1MGYxMmY1Nw\"\n }",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "/Users/webwizards/Documents/repos/ideaw/app/controllers/ApiEaController.php",    "groupTitle": "Metody"  }] });
