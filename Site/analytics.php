<?php
    session_start();
    use DB\Service;

    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {
        require_once('backend/db.php');
        require_once "graphics.php";
        
        $codiceIdentificativo = $_SESSION["Codice_identificativo"];
        $codiceIdentificativo = hash('sha256', $codiceIdentificativo);

        if($codiceIdentificativo == "935f40bdf987e710ee2a24899882363e4667b4f85cfb818a88cf4da5542b0957")
        {
            $arrayMesi = array(
                1 => "Gen",
                2 => "Feb",
                3 => "Mar",
                4 => "Apr",
                5 => "Mag",
                6 => "Giu",
                7 => "Lug",
                8 => "Ago",
                9 => "Set",
                10 => "Ott",
                11 => "Nov",
                12 => "Dic",
            );

            // admin
            $paginaHTML = graphics::getPage("analytics_php.html");
            // Accesso al database
            $connessione = new Service();
            $a = $connessione->openConnection();

            $queryGuadagniMensili = $connessione->get_months_earnigns();

            $month = (int)date('m');
            $year = (int)date('Y');

            $xValues = array();
            $yValues = array();

            for($i = 0; $i < 6; $i++)
            {
                $xValues[$i] = $month . " " . $year;
                $yValues[$i] = 0;
                $month--;
                if($month < 1)
                {
                    $month = 12;
                    $year--;
                }
            }

            if ($queryGuadagniMensili->ok())
            {
                foreach ($queryGuadagniMensili->get_result() as $mese)
                {
                    $index = array_search($mese['mese'] . " " . $mese['anno'], $xValues);

                    if($index !== false)
                    {
                        $xValues[$index] = $arrayMesi[$mese['mese']] . " " . substr($mese['anno'], 2, 2);
                        $yValues[$index] = $mese['totale'];
                    }
                }
                $xValues = array_reverse($xValues);
                $yValues = array_reverse($yValues);

                $valoriChart  = '<input type="hidden" id="jsondataX" value="' . htmlspecialchars(json_encode($xValues), ENT_COMPAT) . '" />';
                $valoriChart .= '<input type="hidden" id="jsondataY" value="' . htmlspecialchars(json_encode($yValues), ENT_COMPAT) . '" />';

                $paginaHTML = str_replace("</valoriChart>", $valoriChart, $paginaHTML);
            }

            // -------------------

            echo $paginaHTML;
        }
        else
        {
            header("Location: index.php");
        }
    }
?>