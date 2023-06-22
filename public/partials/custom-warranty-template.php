<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php print('Warranty PDF'); ?></title>
        <style type="text/css">
            body{
                font-family: 'Helvetica', 'Arial', sans-serif;
            }

            *{
                box-sizing:border-box
            }
            h1 {
                font-size:30px;
                color:#000;
                padding-bottom: 10px;
                text-align:center;
                margin:0;
            }
            h2 {
                font-size:20px;
                color:#c22c3a;

            }
            h3 {
                font-size:16px;
                color:#000;
                margin-bottom: 10px;
                margin-top: 20px;

            }
            .page-table {
                width:100%;
                border-collapse:collapse;
            }
            .text-center {
                text-align:center !important;
            }
            .text-right {
                text-align:right !important;
            }
            .sales-order { margin-bottom:30px;page-break-after: always;}
            .nospace { padding:0 !important;border:0 !important;}
            .page-table { margin-bottom: 15px;border-top:1px solid #d5d5d5;border-right:1px solid #d5d5d5;}
            .page-table th { padding:10px;font-size:14px;color:#777;text-align:left;font-weight: 400;border-bottom:1px solid #d5d5d5;border-left:1px solid #d5d5d5;}
            .page-table td { padding:10px;font-size:16px;color:#000;text-align:left;font-weight: 400;border-bottom:1px solid #d5d5d5;border-left:1px solid #d5d5d5;}
        </style>
    </head>
    <body>
        <div class="page-wrapper">

            <h1>Warranty Details for Warranty ID #%WARR_ID#%</h1>
            <div class="sales-order">
                <h2>Warranty Details</h2>

                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Warranty ID#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#%WARR_ID#%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Date of Creation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%WARR_DATE%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Warranty Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%WARR_TITLE%</td>
                        </tr>
                    </tbody>
                </table>

                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Name of Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%EMP_NAME%</td>
                        </tr>
                    </tbody>
                </table>

                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Address of Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%EMP_ADD%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Phone of Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%EMP_PHONE%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Email of Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%EMP_EMAIL%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Link to Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%EMP_DETAILS%</td>
                        </tr>
                    </tbody>
                </table>

                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Details - Make</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%VEH_MAKE%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Details - Model</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%VEH_MODEL%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Details - Colour</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%VEH_COLOR%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Details - Registration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%VEH_REGI%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Vehicle Registration Details - VIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%VEH_VIN%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Type of Warranty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%TYPE_OF_WARR%</td>
                        </tr>
                    </tbody>
                </table>
                <table class="page-table">
                    <thead>
                        <tr>
                            <th>Created By (Dealership)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>%CREATED_BY%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </body>
</html>