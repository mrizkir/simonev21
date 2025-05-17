<?php

namespace App\Models\Renja;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use App\Models\ReportModel;
use App\Helpers\Helper;

class FormLRAMurniOPDModel extends ReportModel
{   
  public function __construct($dataReport, $print = true)
  {
    parent::__construct($dataReport);
  }
}