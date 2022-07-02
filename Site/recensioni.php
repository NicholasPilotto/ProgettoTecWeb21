<?php
    session_start();

    use DB\Service;
    require_once('backend/db.php');
    require_once "graphics.php";

    if(!isset($_SESSION["Nome"]))
    {
        header("Location: index.php");
    }
    else
    {   
        $paginaHTML = graphics::getPage("recensioni_php.html");

        // Accesso al database
        $connessione = new Service();
        $a = $connessione->openConnection();

        $queryRecensioni = $connessione->get_reviews_by_user($_SESSION["Codice_identificativo"]);

        $listaRecensioni = "<ul id='listaRecensioni'>";

        if($queryRecensioni->ok() && !$queryRecensioni->is_empty())
        {
            $cont = 0;
            $arrayMesi = array(
                "01" => "Gennaio",
                "02" => "Febbraio",
                "03" => "Marzo",
                "04" => "Aprile",
                "05" => "Maggio",
                "06" => "Giugno",
                "07" => "Luglio",
                "08" => "Agosto",
                "09" => "Settembre",
                "10" => "Ottobre",
                "11" => "Novembre",
                "12" => "Dicembre",
            );
            $arrayRecensioni = $queryRecensioni->get_result();

            foreach($arrayRecensioni as $recensione)
            {
                $data = $recensione['datainserimento'];
                $valutazione = $recensione['valutazione'];
                $commento = $recensione['commento'];

                $listaRecensioni .= "<li";
                if($cont++ == 0)
                {
                    $listaRecensioni .= " id='primaRecensione'";
                }
                $listaRecensioni .= " class='recensione'>";

                $listaRecensioni .= "<p class='miniGrassetto'>" . $recensione['titolo'] . "</p>";
                $listaRecensioni .= "<a href='libro.php?isbn=" . $recensione['isbn'] . "'>" . $recensione['isbn'] . "</a>";

                // data
                $arrayData = explode("-", $data);
                $anno = $arrayData[0];
                $mese = $arrayData[1];
                $giorno = $arrayData[2];
                $listaRecensioni .= "<p>" . $giorno . " " . $arrayMesi[$mese] . " " . $anno . "</p>";

                // stelle
                $scrittaStella = "stell";
                $scrittaStella .= ($valutazione == 1) ? "a" : "e";
                $listaRecensioni .= "<p><abbr title='"  . $valutazione .  " " . $scrittaStella . " su 5'>";
                for ($i = 0; $i < 5; $i++)
                {
                    if ($i < $valutazione)
                    {
                        $listaRecensioni .= "<i class='fas fa-star starChecked'></i>";
                    }
                    else
                    {
                        $listaRecensioni .= "<i class='fas fa-star starNotChecked'></i>";
                    }
                }
                $listaRecensioni .= "</abbr></p>";
                $listaRecensioni .= "<p>" . $commento . "</p>";
                
                $listaRecensioni .= "<form action='eliminaRecensione.php' method='post' id='form'>";
                $listaRecensioni .= "<input type='hidden' id='idUtente' value='" . $_SESSION['Codice_identificativo'] . "'/>";
                $listaRecensioni .= "<input type='hidden' id='isbn' value='" . $recensione['libro_isbn'] . "'/>";
                $listaRecensioni .= "<input type='submit' class='button submitEliminaRecensione' value='Elimina recensione'/>";
                $listaRecensioni .= "</form>";
                
                
                
                //$listaRecensioni .= "<a href='eliminaRecensione.php?idUtente=" . $_SESSION['Codice_identificativo'] . "&isbn=" . $recensione['libro_isbn'] . "'>Elimina recensione</a>";
                
                //$valoriChart  = '<input type="hidden" id="jsondataX" value="' . htmlspecialchars(json_encode($xValues), ENT_COMPAT) . '" />';
                
                $listaRecensioni .= "</li>";
            }
        }
        else
        {
            // errore oppure non ci sono recensioni
        }
        $listaRecensioni .= "</ul>";

        /*
        
        if($queryRecensioni->ok())
        {
            $arrayRecensioni = $queryRecensioni->get_result();
            
            foreach($arrayRecensioni as $recensione)
            {

                // stelle
                $scrittaStella = "stell";
                $scrittaStella .= ($valutazione == 1) ? "a" : "e";
                $listaRecensioni .= "<p><abbr title='"  . $valutazione .  " " . $scrittaStella . " su 5'>";

                for ($i = 0; $i < 5; $i++)
                {
                    if ($i < $valutazione)
                    {
                        $listaRecensioni .= "<i class='fas fa-star starChecked'></i>";
                    }
                    else
                    {
                        $listaRecensioni .= "<i class='fas fa-star starNotChecked'></i>";
                    }
                }

                $listaRecensioni .= "</abbr></p>";

                $listaRecensioni .= "<p>" . $commento . "</p>";

                $listaRecensioni .= "</li>";
            }
        }
        else
        {
            $trovatoErrore = true;
        }
        $listaRecensioni .= "</ul>";
        */

        $paginaHTML = str_replace("</listaRecensioni>", $listaRecensioni, $paginaHTML);

        $connessione->closeConnection();
        // -------------------

        echo $paginaHTML;
    }
?>