<? session_start(); ?>
<?php if (empty($pdf)) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php } ?>
    <style>
        .center {
            text-align: center;
        }

        .tdRight {
            text-align: right;
            ;
            font-weight: bold;
        }

        .tdLeft {
            text-align: left;
        }

        .tdTitle {
            text-align: left;
            font-size: 12pt;
            padding: 10px;
            border-collapse: separate;
            border: solid black 1px;
            border-radius: 20px;
            -moz-border-radius: 20px;
            font-weight: bold;
        }

        a {
            color: black
        }

        i {
            color: darkblue;
        }

        body {
            /* border: 5px solid gray;
border-radius: 25px; */
        }

        table {
            padding: 10px;
            /* width: 800px; */
            /* width: 595pt;  */
            width: 100%;
            border-collapse: separate;
            border: solid black 1px;
            border-radius: 5px;
            -moz-border-radius: 5px;

        }

        td {
            padding: 10px;
            font-size: 10pt;
            word-wrap: break-word;
        }

        .td_content {
            font-size: 9pt;
            width: 100%;
        }

        .logo {
            width: 200px;
            padding: 0px;
            margin: 0px;
        }
    </style>

    <?php if (empty($pdf)) { ?>
    </head>

    <body>
    <?php } ?>
    <?
    // echo "hash=".strtoupper(bin2hex(openssl_random_pseudo_bytes(16)));
    // if (empty($_GET['lid']) || empty($_SESSION['company_id'])){die("Wrong ID. Close this page");}
    $hc = $_GET['hc'];

    if (!empty($_POST['tid'])) {
        $tid = $_POST['tid'];
    } elseif (!empty($_GET['tid'])) {
        $tid = $_GET['tid'];
    }

    if (!empty($_GET['prv'])) {
        $prv = $_GET['prv'];
    } else {
        $prv = 0;
    }


    include('../../../pdo/dbconfig.php');

    $query = "select CI.name, CI.short_name from company_infos CI where CI.id=(select company_id from lease_infos LI where LI.hash_code='$hc')";
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (empty($row)) {
        die("Signed or wrong ID");
    };
    $company_name = $row['name'];
    $company_abb = $row['short_name'];

    $lessor_abb_span = '<span style="font-family:Courgette; font-size:12pt">' . $company_abb . '</span>';

    $query = "select  *, LI.id as lease_id, LI.signDT as lease_sign_DT, LI.signIP as lease_sign_IP,  PPT.name as period_name from lease_infos LI left join payment_period_types PPT on LI.payment_period=PPT.id where LI.hash_code='" . $hc . "'";
    // die($query);
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    // $row = $rows[0];
    extract($row);
    //  die(print_r($row));

    $tenant_home_address = "";
    $tenant_city = "";
    $query = "select tenant_id, full_name as tenant_full_name, email as tenant_email, home_address as tenant_home_address, city as tenant_city from tenant_infos where tenant_id=$tid";
    // echo $query;
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);
    list($tenant_firstname, $tenant_surname) = explode(" ", $tenant['tenant_full_name'], 2);
    $tenant_abb = strtoupper(substr($tenant_firstname, 0, 1) . substr($tenant_surname, 0, 1));
    $tenant_abb_span = '<span style="font-family:Courgette; font-size:12pt">' . $tenant_abb . '</span>';
    // var_dump($tenants);
    // die(print_r($tenant_id_array));


    //if(empty($sign_employee_id)){die("ManagerManager did not sign the lease");}
    if (!empty($sign_employee_id)) {
        $query = "select full_name as sign_employee_name, email as sign_employee_email from employee_infos where employee_id=$sign_employee_id";
        // echo $query;
        $stmt = $DB_con->prepare($query);
        $stmt->execute();
        $employee = $stmt->fetch(\PDO::FETCH_ASSOC);
        $sign_employee_name = $employee['sign_employee_name'];
        $sign_employee_email = $employee['sign_employee_email'];
        $manager_sign = "<img src='../../files/lease_signs/manager_sign_$lease_id.png' height='50'>";
        // die(var_dump($employee));
        // die("s".date("H:i:s"));
    } else {
        $employee = " Not Signed by employee";
        $sign_employee_name = "";
        $sign_employee_email = "";
        $manager_sign = "";
    }

    $query = "select  BI.building_name, BI.address as building_address , BI.postal_code as building_postal_code, AI.unit_number from apartment_infos AI left join building_infos BI on BI.building_id=AI.building_id where apartment_id=" . $apartment_id;
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    // $row = $rows[0];
    extract($row);

    $query = "select sign_DT as tenant_sign_DT, sign_IP as tenant_sign_IP from tenant_signs where sign_type_id=1 and lease_id=$lease_id and tenant_id in ($tenant_ids) ";
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!empty($row)) {
        extract($row);
    } else {
        die("Tenant did not sign this lease");
    }


    $pageCounter = 1;
    $pageTotal = 7;
    ?>
    <page>
        <table style="width: 100%;" align="center">
            <tr>
                <td colspan="4" class="center td_content"><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="4" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td colspan="4" class="tdRight" style="font-weight:normal!important;"><b>Certificate ID:</b> <?= $hc ?>
                    &nbsp; &nbsp; &nbsp; &nbsp;<b>Date:</b> <?= date("Y-m-d H:i:s") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="4">Signing Information:</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Signing Name: </td>
                <td colspan="3"><?= $building_name ?>-<?= $unit_number ?> lease</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">ID:</td>
                <td colspan="3"><?= $hash_code ?></td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Status:</td>
                <td colspan="3">Document has been signed by all parties.</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Start Date:</td>
                <td colspan="3"><?= $tenant_sign_DT ?> <b>End Date:</b> <?= $lease_sign_DT ?></td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight"># Signers:</td>
                <td colspan="3">2 # Reviewers: 0 # CC: 1</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Creator:</td>
                <td colspan="3"><?= $sign_employee_name ?> Email: <?= $sign_employee_email ?></td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">IP Address:</td>
                <td colspan="3"><?= $tenant_sign_IP ?></td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Address:</td>
                <td colspan="3"><?= $tenant['tenant_home_address'] . " " . $tenant['tenant_city'] ?></td>
            </tr>
            <tr>
                <td colspan="4" class="tdTitle">Document Information:</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Document Name:</td>
                <td colspan="3"><?= $building_name ?>-<?= $unit_number ?> lease</td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Document ID:</td>
                <td colspan="3"><?= $hc ?></td>
            </tr>
            <tr>
                <td colspan="1" class="tdRight">Pages:</td>
                <td colspan="3"> 7</td>
            </tr>


            <tr>
                <td class="tdTitle" colspan="2">Participant Activity:</td>
                <td class="tdTitle" colspan="2">Signature / Initials:</td>
            </tr>
            <tr>
                <td class="tdRight">Name:</td>
                <td><?= $tenant['tenant_full_name'] ?></td>
                <td><img src='../../files/lease_signs/tenant_sign_<?= $tid ?>_l<?= $lease_id ?>.png' height='50'></td>
                <td><?= $tenant_abb_span; ?></td>
            </tr>
            <tr>
                <td class="tdRight">Email:</td>
                <td><?= $tenant['tenant_email'] ?></td>
                <td>Type:</td>
                <td>Remote Signer</td>
            </tr>
            <tr>
                <td class="tdRight">EULA/TOS/ABP/CCD:</td>
                <td colspan="3">Accepted: <?= $tenant_sign_DT ?> [IP:<?= $tenant_sign_IP ?>]</td>
            </tr>
            <tr>
                <td class="tdRight">Document:</td>
                <td colspan="3">Signed and Accepted – date/time: <?= $tenant_sign_DT ?> [IP:<?= $tenant_sign_IP ?>]</td>
            </tr>

            <tr>
                <td class="tdRight">Name:</td>
                <td><?= $sign_employee_name ?></td>
                <td><?= $manager_sign ?></td>
                <td><?= $lessor_abb_span ?></td>
            </tr>
            <tr>
                <td class="tdRight">Email:</td>
                <td><?= $sign_employee_email ?></td>
                <td>Type:</td>
                <td>Remote Signer</td>
            </tr>
            <!-- <tr>
            <td class="tdRight">EULA/TOS/ABP/CCD:</td>
            <td colspan="3">Accepted: 6/10/2020 2:48:49 PM PDT [IP:70.48.116.253]</td>
        </tr> -->
            <tr>
                <td class="tdRight">Document:</td>
                <td colspan="3">Signed and Accepted – date/time: <?= $lease_sign_DT ?> [IP:<?= $lease_sign_IP ?>]</td>
            </tr>

        </table>



        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++;;
            $pageCounter++; ?>
        </page_footer>
    </page>

    <page>
        <table align="center">
            <tr>
                <td colspan="2" class=center><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="2" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td class="tdRight">Certificate ID: <?= $hc ?></td>
                <td>Date: <?= date("Y-m-d") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="2">Agreement Between Parties / Terms of Service:</td>
            </tr>
            <tr>
                <td colspan="2" class="td_content"><b>Terms Of Service</b><br>
                    <p>DigitalSign is a service offered by SPG Management. This is a legal agreement, by and between
                        You ('You' may be either an individual or a single entity) and SPG Management for the sole
                        purpose of use by You of the DigitalSign service
                        offered by SPG Management (the 'Service'). SPG Management and You may be referred to herein as
                        the 'Parties'. When using the Service, You
                        agree to be bound by and subject to any guidelines, policies, rules or additional terms
                        applicable to the Service which SPG Management may
                        communicate to You or post from time to time on the spgmanagement.com website. These guidelines,
                        policies, rules or additional terms are considered
                        included as part of this DigitalSign Service End User License Agreement (this 'Agreement'). SPG
                        Management reserves the right to amend this
                        Agreement from time to time and will post material changes to this Agreement on its web site. If
                        you continue to use the Service once Instanet
                        Solutions has published the changes to the Agreement, You will be deemed to have accepted and
                        agreed to those changes.
                        If You are accessing the Service to view, edit, electronically sign or retrieve an electronic
                        document that was made available to You by one of
                        SPG Management' other customers, You explicitly acknowledge and agree that: (i) You are using
                        the Service for such purpose, (ii) recognize the
                        Service provides a web based security service that enables users to verify the authenticity of
                        documents, provide tamper detection, digitally sign,
                        electronically date, time stamp and postmark, and store such documents, and (iii) the Service,
                        together with the Adobe/GlobalSign CDS digital
                        signature timestamp certification, is a qualified security procedure. In addition, You
                        acknowledge and agree that your use of the Service, together
                        with the Adobe/GlobalSign CDS digital signature timestamp certification, (i) is commercially
                        reasonable under the circumstances for which You
                        employ its use; (ii) is being applied by You in a trustworthy manner, and (iii) is being relied
                        upon by You in a reasonable and good faith manner.
                        End User License Agreement</p>
                    <b>1. USER ACCOUNT, PASSWORD, AND SECURITY</b><br>
                    <p>To open an account, you must complete the registration process by providing Concepts In Data
                        Management Inc. US d.b.a. SPG Management with
                        current, complete and accurate information as prompted by the Service Order Registration Form or
                        via phone to a SPG Management customer
                        support representative. You then will receive a password and an account first and last name. You
                        are entirely responsible for maintaining the
                        confidentiality of your password and account. Furthermore, you are entirely responsible for any
                        and all activities that occur under your account. You
                        understand and acknowledge that by opening an account and utilizing the Services (as defined
                        below) you are agreeing to be bound by these
                        Terms of Service (TOS) and thereby enter into an agreement with SPG Management with respect
                        thereto.
                        You agree to notify SPG Management immediately of any unauthorized use of your account or any
                        other breach of security.
                        BY CLICKING THE `I ACCEPT` BUTTON , YOU AGREE TO THE TERMS OF USE OF THE SPG Management, AND ALL
                        WEB SITES
                        RELATED THERETO (THE “SERVICES”).</p>
                    <b>2. USER PRIVACY</b><br>
                    <p>It is SPG Management' policy to respect the privacy of its users. SPG Management will not
                        monitor, edit, or disclose any personal information about
                        you or your use of the Services, including its contents, without your prior permission unless
                        SPG Management has a good faith belief that such
                        action is necessary to: (1) conform to legal requirements or comply with legal process; (2)
                        protect and defend the rights or property of Instanet
                        Solutions (3) enforce these TOS; or (4) act to protect the interests of its users or others. For
                        more information, see the Services' Privacy Statement
                        at https://www.spgmanagement.com/admin/terms.php</p>
                    <p>Some personal information you provide to SPG Management may be stored outside of the country in
                        which you reside.
                        You agree that SPG Management may access your account, including its contents, as stated above
                        or to respond to Services or technical issues.</p>
                    <b>3. DATA STORAGE AND OTHER LIMITATIONS</b><br>
                    <p>You agree that SPG Management is not responsible or liable for the deletion or failure to store
                        form data or other information.</p>
                    <b>4. USER CONDUCT</b><br>
                    <p>As a condition of your use of the Services, you warrant to SPG Management that you will not use
                        the Services for any purpose that is unlawful or
                        prohibited by these TOS. Any unauthorized use of the Services, or the resale of its Services, is
                        expressly prohibited. You agree to abide by all
                        applicable local, state, national and international laws and regulations and are solely
                        responsible for all acts or omissions that occur under your
                        account or password, including the content of your transmissions through the Services. By way of
                        example, and not as a limitation, you agree not
                        to:</p>
                </td>
            </tr>

        </table>


        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++; ?>
        </page_footer>
    </page>

    <page>


        <table align="center">
            <tr>
                <td colspan="2" class=center><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="2" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td class="tdRight">Certificate ID: <?= $hc ?></td>
                <td>Date: <?= date("Y-m-d") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="2">Agreement Between Parties / Terms of Service:</td>
            </tr>
            <tr>
                <td colspan="2" class="td_content">
                    <p><b>Defame, abuse, harass, stalk, threaten or otherwise violate the legal rights (such as rights
                            of privacy and publicity) of others. Publish,
                            distribute or disseminate any inappropriate, profane, defamatory, infringing, obscene,
                            indecent or unlawful material or information.
                            Harvest or otherwise collect information about others, including email addresses, without
                            their consent.</b> Transmit or upload any material
                        that contains viruses, trojan horses, worms, time bombs, cancelbots, or any other harmful or
                        deleterious programs. Transmit or upload any material
                        that contains software or other material protected by intellectual property laws, rights of
                        privacy or publicity or any other applicable law unless you
                        own or control the rights thereto or have received all necessary consents. Interfere with or
                        disrupt networks connected to the Services or violate the
                        regulations, policies or procedures of such networks. Attempt to gain unauthorized access to the
                        Services, other accounts, computer systems or
                        networks connected to the Services, through password mining or any other means. Violate any
                        applicable laws or regulations including, without
                        limitation, laws regarding the transmission of technical data or software exported from the
                        United States through the Services.
                        Interfere with another's use and enjoyment of the Services or another individuals' or entity's
                        use and enjoyment of similar services. Instanet
                        Solutions has no obligation to monitor the Services or any user's use thereof or retain the
                        content of any user session. SPG Management has no
                        obligation to investigate a user's identity or verify the authenticity of a user's statements,
                        including those made to open an account. However,
                        SPG Management reserves the right at all times to monitor, review, retain and/or disclose any
                        information as necessary to satisfy any applicable law, regulation, legal process or
                        governmental request.</p>
                    <b>5. LINKS TO THIRD PARTY SITES</b><br>
                    <p>THE LINKS INCLUDED WITHIN THE SERVICES MAY LET YOU LEAVE THE SERVICES WEB SITES ('LINKED SITES').
                        THE LINKED SITES
                        ARE NOT UNDER THE CONTROL OF SPG Management AND SPG Management IS NOT RESPONSIBLE FOR THE
                        CONTENTS
                        OF ANY LINKED SITE OR ANY LINK CONTAINED IN A LINKED SITE, OR ANY CHANGES OR UPDATES TO SUCH
                        SITES. INSTANET
                        SOLUTIONS IS NOT RESPONSIBLE FOR WEBCASTING OR ANY OTHER FORM OF TRANSMISSION RECEIVED FROM ANY
                        LINKED SITE.
                        SPG Management IS PROVIDING THESE LINKS TO YOU ONLY AS A CONVENIENCE, AND THE INCLUSION OF ANY
                        LINK DOES NOT
                        IMPLY ENDORSEMENT BY SPG Management OF THE SITE OR ANY ASSOCIATION WITH THEIR OPERATORS.</p>
                    <b>6. DISCLAIMERS/LIMITATION OF LIABILITY</b><br>
                    <p>The information included in or available through the Services may include inaccuracies or
                        typographical errors. Changes are periodically added to
                        such information as deemed appropriate by SPG Management and/or its respective suppliers may
                        make improvements and/or changes in the
                        Services at any time.</p>
                    <p> SPG Management does not represent or warrant that the Services will be uninterrupted or
                        error-free, that defects will be corrected, or that the
                        Services or the server that makes them available, are free of viruses or other harmful
                        components. SPG Management does not warrant or
                        represent that the use or the results of the use of the Services or the materials made available
                        as part of the Services will be correct, accurate,
                        timely, or otherwise reliable.</p>
                    <p>You specifically agree that SPG Management shall not be responsible for unauthorized access to or
                        alteration of your transmissions or data, any
                        material or data sent or received or not sent or received, or any transactions entered into
                        through the Services. You specifically agree that Instanet
                        Solutions is not responsible or liable for any threatening, defamatory, obscene, offensive or
                        illegal content or conduct of any other party or any
                        infringement of another's rights, including intellectual property rights. You specifically agree
                        that SPG Management is not responsible for any
                        content sent using and/or included in the Services by any third party.</p>
                    <p> SPG Management AND/OR ITS RESPECTIVE SUPPLIERS MAKE NO REPRESENTATIONS ABOUT THE SUITABILITY,
                        RELIABILITY,
                        AVAILABILITY, TIMELINESS, AND ACCURACY OF THE SERVICES FOR ANY PURPOSE. THE SERVICES ARE
                        PROVIDED “AS IS”.
                        WITHOUT WARRANTY OF ANY KIND. SPG Management AND/OR ITS RESPECTIVE SUPPLIERS HEREBY DISCLAIM ALL
                        WARRANTIES AND CONDITIONS WITH REGARD TO THE SERVICES, INCLUDING ALL IMPLIED WARRANTIES AND
                        CONDITIONS OF
                        MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE AND NON-INFRINGEMENT.</p>

                </td>
            </tr>

        </table>


        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++; ?>
        </page_footer>
    </page>


    <page>


        <table align="center">
            <tr>
                <td colspan="2" class=center><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="2" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td class="tdRight">Certificate ID: <?= $hc ?></td>
                <td>Date: <?= date("Y-m-d") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="2">Agreement Between Parties / Terms of Service:</td>
            </tr>
            <tr>
                <td colspan="2" class="td_content">
                    <p>IN NO EVENT SHALL SPG Management AND/OR ITS SUPPLIERS BE LIABLE FOR ANY DIRECT, INDIRECT,
                        PUNITIVE, INCIDENTAL,
                        SPECIAL, CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER INCLUDING, WITHOUT LIMITATION, DAMAGES
                        FOR LOSS OF
                        USE, DATA OR PROFITS, ARISING OUT OF OR IN ANY WAY CONNECTED WITH THE USE OR PERFORMANCE OF THE
                        SERVICES OR
                        RELATED WEB SITES, WITH THE DELAY OR INABILITY TO USE THE SERVICES OR RELATED WEB SITES, THE
                        PROVISION OF OR
                        FAILURE TO PROVIDE SERVICES, OR FOR ANY INFORMATION, SOFTWARE, PRODUCTS, SERVICES AND RELATED
                        GRAPHICS
                        OBTAINED THROUGH THE SERVICES, OR OTHERWISE ARISING OUT OF THE USE OF THE SERVICES, WHETHER
                        BASED ON
                        CONTRACT, TORT, NEGLIGENCE, STRICT LIABILITY OR OTHERWISE, EVEN IF SPG Management OR ANY OF ITS
                        SUPPLIERS HAS
                        BEEN ADVISED OF THE POSSIBILITY OF DAMAGES. BECAUSE SOME STATES/JURISDICTIONS DO NOT ALLOW THE
                        EXCLUSION OR
                        LIMITATION OF LIABILITY FOR CONSEQUENTIAL OR INCIDENTAL DAMAGES, THE ABOVE LIMITATION MAY NOT
                        APPLY TO YOU. IF
                        YOU ARE DISSATISFIED WITH ANY PORTION OF THE SERVICES, OR WITH ANY OF THESE TERMS OF USE, YOUR
                        SOLE AND
                        EXCLUSIVE REMEDY IS TO DISCONTINUE USING THE SERVICES AND THEIR RELATED WEB SITES.</p>
                    <b>7. INDEMNIFICATION</b><br>
                    <p>You agree to indemnify and hold SPG Management, its parents, subsidiaries, affiliates, officers
                        and employees, harmless from any claim, demand,
                        or damage, including reasonable attorneys' fees, asserted by any third party due to or arising
                        out of your use of or conduct on the Services.</p>
                    <b>8. TERMINATION</b><br>
                    <p> SPG Management may terminate your access to any part or all of the Services and any related
                        Services at any time, with or without cause, with or
                        without notice, effective immediately, for any reason whatsoever.</p>
                    <p>If you wish to terminate your account, your only recourse is to discontinue the use of the
                        Services.
                        SPG Management shall have no obligation to maintain any content in your account or to forward
                        any contract/transaction information to you or any
                        third party.</p>
                    <b>9. PARTICIPATION IN PROMOTIONS OF ADVERTISERS</b><br>
                    <p>Any dealings with advertisers on the Services or participation in promotions, including the
                        delivery of and the payment for goods and services, and
                        any other terms, conditions, warranties or representations associated with such dealings or
                        promotions, are solely between you and the advertiser
                        or other third party. SPG Management shall not be responsible or liable for any part of any such
                        dealings or promotions.</p>
                    <b>10. USE OF SERVICES</b><br>
                    <p>If you are accessing the SPG Management Services to view, sign or retrieve a document that was
                        made available to you through the Services,
                        SPG Management grants you a limited license to access the Services solely to use and learn about
                        the Services. Other than viewing, signing,
                        modifying or retrieving such document, you may not modify, copy, distribute, transmit, display,
                        perform, reproduce, duplicate, publish, license,
                        create derivative works from, offer for sale, or use in any other way the Services or any
                        information contained in, or obtained from, the Services
                        without the express written consent of SPG Management. Any and all unauthorized uses of the
                        Services or the contents therein will terminate the
                        limited license granted to you. Without SPG Management' express written consent, you may not (a)
                        use any automated means to access the Services or collect any information from the Services
                        (including, without limitation, robots, spiders or scripts), (b) use the Services in any manner
                        that could damage, disable, overburden, or impair the Services or interfere with any other
                        user's use or enjoyment of the Services, or (c) from the
                        Services' web sites, place pop-up windows over its pages, or otherwise affect the display of its
                        pages.</p>
                    <b>11. MODIFICATIONS TO TERMS OF SERVICES, MEMBER POLICIES</b><br>
                    <p> SPG Management reserves the right to change the TOS or policies regarding the use of the
                        Services at any time and to notify you by posting an
                        updated version of the TOS on this web site. You are responsible for regularly reviewing the
                        TOS. Continued use of the Services after any such
                        changes shall constitute your consent to such changes.</p>

                </td>
            </tr>

        </table>

        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++; ?>
        </page_footer>
    </page>

    <page>

        <table align="center">
            <tr>
                <td colspan="2" class=center><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="2" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td class="tdRight">Certificate ID: <?= $hc ?></td>
                <td>Date: <?= date("Y-m-d") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="2">Agreement Between Parties / Terms of Service:</td>
            </tr>
            <tr>
                <td colspan="2" class="td_content">
                    <b>12. GENERAL</b><br>
                    <p>These TOS and the agreement entered into by you with SPG Management pursuant hereto are governed
                        by the laws of the Province of Ontario,
                        and Canada. Use of the Services are unauthorized in any jurisdiction that does not give effect
                        to all provisions of these TOS, including, without
                        limitation, this paragraph. You agree that no joint venture, partnership, employment, or agency
                        relationship exists between you and Instanet
                        Solutions as a result of this agreement or use of the Services. SPG Management' performance of
                        this agreement is subject to existing laws and
                        legal process, and nothing contained in this agreement is in derogation of SPG Management' right
                        to comply with governmental, court and law
                        enforcement requests or requirements relating to your use of the Services or information
                        provided to or gathered by SPG Management with respect
                        to such use. If any part of these TOS or the agreement between you and SPG Management is
                        determined to be invalid or unenforceable pursuant
                        to applicable law including, but not limited to, the warranty disclaimers and liability
                        limitations set forth above, then the invalid or unenforceable
                        provision will be deemed superseded by a valid, enforceable provision that most closely matches
                        the intent of the original provision and the
                        remainder of the TOS and agreement shall continue in effect. Unless otherwise specified herein,
                        these TOS and this agreement constitutes the
                        entire agreement between the user and SPG Management with respect to the Services (excluding the
                        use of any software which may be subject to
                        an end-user license agreement) and it supersedes all prior or contemporaneous communications and
                        proposals, whether electronic, oral or written,
                        between the user and SPG Management with respect to the Services. A printed version of these TOS
                        and this agreement and of any notice given in
                        electronic form shall be admissible in judicial or administrative proceedings based upon or
                        relating to these TOS and this agreement to the same
                        extent and subject to the same conditions as other business documents and records originally
                        generated and maintained in printed form. You and
                        SPG Management agree that any cause of action arising out of or related to the Services must
                        commence within one (1) year after the cause of
                        action arose; otherwise, such cause of action is permanently barred. The section titles in these
                        TOS are solely used for the convenience of the
                        parties and have no legal or contractual significance.</p>
                    <b>13. LANGUAGE</b><br>
                    <p>It is the express will of the parties that this agreement and all related documents have been
                        drawn up in English. COPYRIGHT AND TRADEMARK
                        NOTICES: All contents of the Services are: Copyright © 2010 SPG Management Inc. and/or its
                        suppliers, c/o Concepts in Data Management
                        Incorporated, PO Box 220 Lambeth Station, London, Ontario N6P1P9 Canada. All information related
                        to the Services, including, without limitation,
                        text, graphics, web sites and other files, and the arrangement thereof, are copyrighted and SPG
                        Management reserves all rights associated with
                        such copyrights.</p>
                    <b>TRADEMARKS.</b><br>
                    <p>The names, trademarks, service marks and logos appearing within or related to the Services may
                        not be used in any advertizing or publicity, or
                        otherwise to indicate SPG Management' sponsorship or affiliation with any product, service,
                        event or organization without SPG Management' prior
                        express written permission. SPG Management' MRED FAX PLUS, TRANSACTIONDESK, DigitalSign, AS2GO,
                        INSTANET FORMS,
                        INSTANET FAX, DOCBOX and DOCBOX2GO and/or other SPG Management products and Services referenced
                        herein or within the Services are
                        either trademarks or registered trademarks of SPG Management.</p>
                    <p>Any rights not expressly granted herein are reserved.</p>
                    <p style="font-size:larger"><b>Agreement Between Parties</b></p>
                    <p>You are accessing the DigitalSign Service (the “Service”) to view, edit, electronically sign and
                        retrieve an electronic document that was made
                        available to you by one of SPG Management' other customers. This is an agreement by and between
                        or among you and the other parties to such
                        electronic document. You explicitly acknowledge and agree that all parties to such electronic
                        document have mutually agreed to the use of the
                        Service and that you, together with such other parties: (i) are using the Service for such
                        purpose, (ii) recognize the Service, in conjunction with the
                        Adobe/GlobalSign CDS digital signature timestamp certification, provides a web based security
                        service that enables users to verify the authenticity
                        of documents, provide tamper detection, digitally sign, electronically date and time, and store
                        such documents, and (iii) agree that the Service,
                        together with the Adobe/GlobalSign CDS digital signature timestamp certification, is a qualified
                        security procedure. In addition, you, together with
                        each party to the electronic document, acknowledge and agree that your use of the Service,
                        together with the Adobe/GlobalSign CDS digital
                        signature timestamp, (i) is commercially reasonable under the circumstances for which you employ
                        its use; (ii) is being applied by you in a
                        trustworthy manner, and (iii) is being relied upon by you in a reasonable and good faith manner.
                    </p>
                    <p>Last Update: 07212013</p>


                </td>
            </tr>

        </table>

        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++; ?>
        </page_footer>
    </page>

    <page>

        <table align="center">
            <tr>
                <td colspan="2" class=center><img class="logo" src="../../../images/logo_big.jpg"></td>
            </tr>
            <tr>
                <td colspan="2" class=center style="font-size: 20px; font-weight:bold">Signing Certificate</td>
            </tr>
            <tr>
                <td class="tdRight">Certificate ID: <?= $hc ?></td>
                <td>Date: <?= date("Y-m-d") ?></td>
            </tr>
            <tr>
                <td class="tdTitle" colspan="2">Consumer Consent Disclosure:</td>
            </tr>
            <tr>
                <td colspan="2" class="td_content">
                    <p><b>Consumer Consent Disclosure</b></p>
                    <p>By proceeding and selecting the "I Agree" toggle button option corresponding to the Consumer
                        Consent Disclosure section on the DigitalSign Signature
                        Creation Wizard you are agreeing that you have reviewed the following consumer consent
                        disclosure information and consent to transacting business
                        electronically, to receive notices and disclosures electronically, and to utilize electronic
                        signatures instead of using paper documents. This electronic signature
                        service (“DigitalSign”) is provided on behalf of our client (“Sender”) who listed with their
                        contact information at the bottom of the DigitalSign Signing
                        Participant email (“Invitation”) you received. The Sender will be sending electronic documents,
                        notices, disclosures to you or requesting electronic
                        signatures from you.</p>
                    <p>You are not required to receive disclosures, notices or sign documents electronically. If you
                        prefer not to do so, you can make a request to receive paper
                        copies and withdraw your consent to conduct business electronically at any time as described
                        below.</p>
                    <b>Scope of Consent</b><br>
                    <p>You agree to receive electronic notices, disclosures, and electronic signature documents with all
                        related and identified documents and disclosures provided
                        over the course of your relationship with the Sender. You may at any point withdraw your consent
                        by following the procedures described below.</p>
                    <b>Hardware and Software Requirements</b><br>
                    To receive theaboveinformationelectronically,you willneed all of the following:
                    <ul>
                        <li>a computer or tablet device with internet access</li>
                        <li>a working individual email address</li>
                        <li>a supported operating systems and browsers from list table below</li>
                    </ul>
                    OperatingSystem: WindowsXPSP3, Windows Vista, Windows7/8, MacOS X10.5 (Leopard™), Apple – IOS > 5.0
                    Internet Explorer > 10, Apple Safari > 5.0, Mozilla® Firefox > 23, Mobile Safari > 5, Chrome > 22.






                    JavaScript and Cookies mustbe enabled in the browser.<br>
                    <b>Requesting Paper Copies</b><br>
                    <p>You have the ability to download and print or download any disclosures, notices or signed
                        documents made available to you through DigitalSign using the
                        document print options located within the service. DigitalSign can also email you a copy of all
                        documents you sign electronically. You are not required to
                        receive disclosures, notices or sign documents electronically and may request paper copies of
                        documents or disclosures if you prefer. If you do not wish to
                        work with electronic documents and instead wish to receive paper copies you can contact the
                        Sender though DigitalSign document signing interface or
                        request paper copies by following the procedures described below. There could be fees associated
                        to printing and delivering the paper documents.</p>
                    <b>Withdrawal of Consent to Conduct Business Electronically</b><br>
                    <p>Consent to receive electronic documents, notices or disclosures can be withdrawn at any time. In
                        order to withdraw consent you must notify the Sender. You
                        may withdraw consent to receive electronic notices and disclosures and optionally electronically
                        signatures by following the procedures described below.</p>
                    <b>Requesting paper documents, withdrawing consent, and/or updating contact information</b><br>
                    <p>To request paper copies of documents, withdraw consent to conduct business electronically and
                        receive documents, notices, or disclosures electronically or
                        sign documents electronically please contact the Sender by sending an email to Sender’s email
                        address located at the bottom of the Invitationrequesting
                        your desired action. Use one of the following email subject lines and insert the associated text
                        into the body of the email:</p>
                    <ul>
                        <li>Email Subject line: "Request for Paper Documents"<br>
                            Include your full name, email address, telephone number, postal address and the signing name
                            found in the Invitation in the body of the email.
                            Note: <i>There could be per page and delivery fees required by the Sender to send the paper
                                documents.</i></li>
                        <li>Email Subject line: “Withdraw Consent to Conduct Business Electronically”
                            Include your full name, email address, telephone number, postal address and the signing name
                            found in the Invitation in the body of the email.
                        </li>
                        <li>Email Subject line: "Update Contact Information"<br>
                            Include your full name, email address, telephone number, postal address and the signing name
                            found in the Invitation in the body of the email.
                            along with the requested change(s) to your contact information
                        </li>
                    </ul>

                </td>
            </tr>

        </table>
        <page_footer>
            <? echo "page $pageCounter of $pageTotal";
            $pageCounter++; ?>
        </page_footer>
    </page>


    <?php if (empty($pdf)) { ?>
    </body>

    </html>
<? } ?>