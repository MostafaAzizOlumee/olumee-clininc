<?php
class PrescribedMedicine extends BaseModel{
  protected $tbl = 'prescribed_medicine';

  public function getByPrescription($prescriptionId) {

        $sql = "
        SELECT 
            pm.medicine_total_usage,
            pm.medicine_usage_frequency,
            pm.medicine_usage_form,
            pm.medicine_doctor_note,
            m.generic_name,
            m.dose,
            mt.name AS medicine_type
        FROM prescribed_medicine pm
        INNER JOIN medicine m ON m.id = pm.medicine_id
        INNER JOIN medicine_type mt ON mt.id = m.medicine_type_id
        WHERE pm.prescription_id = '$prescriptionId'";

         $result =  $this->raw($sql);

        return ($result && $result->num_rows > 0) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;
    }
}