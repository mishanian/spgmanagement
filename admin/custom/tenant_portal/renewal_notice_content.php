<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// $params = array("lease_id"=>$lease_id,"tenant_id"=>$tenant_id,
//     "sign"=> $sign, "logo" => $logo, "end_date" => $end_date, "next_length_of_lease" => $next_length_of_lease, "pdf" => $pdf, "renewal_notice_date" => $renewal_notice_date, "unit_number" => $unit_number, "address" => $address,
//     "city" => $city, "province_name" => $province_name, "postal_code" => $postal_code,  "tenant_name" => $tenant_name, "last_day_renewal"=>$last_day_renewal,
//     "monthly_amount" => $monthly_amount, "lease_status_id" => $lease_status_id, "is_signed"=>$is_signed, "email"=>$email, "renewal_letter_date"=>$renewal_letter_date,
// );
function render_renewal($params)
{
    extract($params);


    // die("lease_status_id=$lease_status_id last_day_renewal=$last_day_renewal empty=$empty");
    $receive_date = !empty($renewal_notice_date) && $renewal_notice_date != "0000-00-00" ? $renewal_notice_date : "-";
    if (empty($empty)) {
        $empty = 0;
    }
    if (empty($renewal_letter_date)) {
        $renewal_letter_date = date("Y-m-d");
    }
    $end_date = date("Y-m-t", strtotime($end_date));
    $next_start_date = date('Y-m-d', strtotime($end_date . ' + 1 days'));
    $next_end_date = date("Y-m-t", strtotime($end_date . " + $next_length_of_lease days"));
    // die("end_date=$end_date next_start_date=$next_start_date next_end_date=$next_end_date");
    if ($pdf == 1 || $email == 1) {
        $table_class = "  width: 100%;";
    } else {
        $table_class = "";
    }
    $text = "
        <table class='table' style='$table_class'>
        <tr>
            <td>
                <p>Date:  " . $renewal_letter_date . " <br />
        # $unit_number / $address <br />
        $city , $province_name , $postal_code </p>
        </td>
        <td>";

    if (!empty($logo)) {
        // $text .= "<img src='https://spgmanagement.com/admin/files/logos/$logo'>";
    }
    $text .= "
        &nbsp;</td>
        </tr>

        <tr>
            <td title='English' style='width:50%; word-wrap:break-word;'>
                <p>
                    Dear $tenant_name,</p>


                <p>Your lease will expire on  $end_date, We are pleased to offer you the option of renewing
                    your current Lease for next term with following condition for the dwelling indicated above:</p>

                <p><b>1. Residential Lease</b><br />
                    Monthly Rental Amount:  $monthly_amount <br />
                    <b>Lease Term</b>
                    From $next_start_date to  $next_end_date
                </p>
                ";

    $text .= "
        <p>The cost of the rental increase will be cover for the adjustment of the property tax, school tax,
            energy cost, maintenance cost.</p>
        <p>Please remember that if you choose to terminate the Lease,
            you must provide us <b>written notice within 30 days</b> from the day of receive this notice.
        </p>
        <u>Failure to provide this required notice indicates your approval to continue the Lease with this
            rent increase automatically.</u>
        <p><b>Important Reminder:</b> You need to come to office to renew your electric access card if you
            decide to stay another lease term. Your card will expire if you don’t renew. You need to provide
            a copy of dwelling insurance renewal with your lease renewal before the new lease term. Or your
            lease will be null and void on
            the termination of your current lease term.</p>
        </td>



        <td title='French' style='width:50%; word-wrap:break-word; vertical-align: top;'>
            <p>Chers $tenant_name,</p>

            <p>Votre bail sera à échéance le  $end_date, Nous sommes heureux de vous offrir la
                possibilité de renouveler votre bail actuel pour l'année prochaine avec la condition suivant :
            </p>

            <p><b>1. Bail résidentiel</b><br />
                Loyer mensuel :  $monthly_amount <br />
                <b>Durée de Bail :</b>
                Du  $next_start_date  à  $next_end_date
            </p>";


    $text .= "
        <p>Le cout de l'augmentation de loyer sera couvert pour l'ajustement de la taxe foncière,
            taxe scolaire, le cout de l'énergie, le cout de maintenance.</p>
        <p>Rappelez-vous que si vous choisissez de résilier votre bail, vous devez fournir le
            préavis écrit requis <b>dans 30 jours</b> de votre réception de cet avis.</p> <u>En défaut
                de fournir ce préavis requis est indiquée votre acception de continuer le bail avec
                cette augmentation de Loyer.</u>
        <p><b>Rappel important :</b> Vous devez venir au bureau pour réactiver votre carte d'accès,
            si vous décidez de rester pour le nouveau bail. Votre carte d’accès sera expirée à la
            fin de votre bail actuel. Vous devez aussi nous fournissez un renouvellement d’assurance
            pour votre logement avant échéance de bail, ou votre bail sera nulle à la fin de votre
            bail actuel.</p>


        </td>
        </tr>";

    if (!empty($terms_en) && !empty($terms_fr)) {
        $text .= "
            <tr>
                <td title='English' style='width:50%; word-wrap:break-word; vertical-align: top;'>
                    <p>
                        $terms_en
                    </p>
                </td>
                <td title='French' style='width:50%; word-wrap:break-word; vertical-align: top;'>
                    <p>
                        $terms_fr
                    </p>
                </td>
            </tr>";
    }


    $text .= "
        <tr>
            <td colspan='2'>";


    if ($email == 0 && !empty($sign) && date("Y-m-d") < $last_day_renewal) {

        $text .= "<p><img src='./images/$sign' width='50'>";
    }
    $text .= "<br />Management Office</p>
            </td>
        </tr>

        ";
    $text .= "<tr><td colspan=2>Lease from <b>$next_start_date</b> to <b>$next_end_date</b> <br>
        Name: <b>$tenant_name</b> Address: # <b>$unit_number / $address</b> Rent: <b>$monthly_amount</b><br>
        I have received the notice on: <b>$receive_date</b><br>
        Signature:
        </td></tr>";
    if ($email == 0 && date("Y-m-d") < $last_day_renewal) {
        if (empty($pdf) && in_array($lease_status_id, array(1, 7)) || $empty == 1) {
            $text .= "<tr>
            <td style='width:50%; word-wrap:break-word; vertical-align: top;'>
                <p>
                    <input type='radio' value='1' name='accept' id='accept'>
                    <b>J'accepte le nouveau loyer et renouveler le bail<br />
                    I accept the new rent and will renew the lease</b>

                </p>
            </td>
            <td>
                <p>
                    <input type='radio' value='0' name='accept' id='accept'>
                    <b>Je n'accepte pas le nouveau loyer. Je vais quitter les lieux avant la fin du bail<br />
                    I don't accept the new rent & will be vacating the premises before the end of the lease.</b>
                </p>
                <p>Je connais quelqu'un qui est intéresse cet appartement.<br />
                    I know someone who is interested in renting the apartment.</p>
                <p>Applicant Phone: <input type='text' name='phone'></p>
            </td>
        </tr>
<tr><td colspan=2>
<input type='radio' value='3' name='accept' id='accept'> <b>Skip for now and I will response within 30 days. </b>
</td></tr>
        <tr>
            <td colspan=2>


                Signature";
            if (empty($pdf)) {
                $text .= "<div id='signature' style=''>
                    <canvas id='signature-pad' class='signature-pad' width='300px' height='200px'></canvas>
                </div><br />
                <input type='hidden' name='signatureData' id='signatureData'>
                <input class='btn btn-primary' type='submit' name='submit' value='Submit' />
                <button class='btn btn-danger' type='button' onclick='window.signaturePad.clear();'>Clear
                    Signature</button>";
            }

            $text .= "
            </td>
        </tr>";
        }
        /*
        if ((empty($pdf) && in_array($lease_status_id, array(8)) || $empty == 1) && date("Y-m-d") < $last_day_renewal) {
            $text .= "<tr><td>
<input type='radio' value='3' name='accept' id='accept'> <b>Skip for now and I will response within 30 days </b>
</td></tr>
        <tr>
            <td colspan=2>


                Signature";
            if (empty($pdf)) {
                $text .= "<div id='signature' style=''>
                    <canvas id='signature-pad' class='signature-pad' width='300px' height='200px'></canvas>
                </div><br />
                <input type='hidden' name='signatureData' id='signatureData'>
                <input class='btn btn-primary' type='submit' name='submit' value='Submit' />
                <button class='btn btn-danger' type='button' onclick='window.signaturePad.clear();'>Clear
                    Signature</button>";
            }
        }
        */
        $text .= "<tr>
            <td>";

        if ($lease_status_id == 10) {
            $text .= "<p><b> J'accepte le nouveau loyer et renouveler le bail<br />
                    I accept the new rent and will renew the lease</b></p>";
        }
        if ($lease_status_id == 9) {
            $text .= "
                <p><b> Je n'accepte pas le nouveau loyer. Je vais quitter les lieux avant la fin du bail<br />
                    I don't accept the new rent & will be vacating the premises before the end of the lease.</b></p>";
        }
        $text .= "
            </td>
            <td>";
        //if(in_array($lease_status_id,array(9,10))){
        $sign_url = "../../files/tenant_signatures/renew_signature_l" . $lease_id . "_t" . $tenant_id . ".png";
        // echo $sign_url . "----" . $pdf;
        // die("pdf=$pdf is_signed=$is_signed post submit=" . $_POST['submit']);
        if ((!empty($pdf) || !empty($_POST['submit'])) && $is_signed == true) { // && file_exists($sign_url)
            $text .= "Tenant Sign on " . date("Y-m-d H:i:s") . " <img src='$sign_url'>";
        }
        $text .= "</td></tr>";


        $text .= "</table>";

        if (empty($pdf)) {
            $text .= "</div>
            </form>";
        }
    } else {
        $text .= "<tr><td colspan=2>The last day for sign the renewal online was $last_day_renewal. Renewal Notice Date=$renewal_notice_date<br>
        <a href='../../index.php'>Home</a>
        </td></tr>
        </table>";
    }
    return $text;
}
