<?php

class PatientPrescription extends BaseModel{
  protected $tbl = 'patient_prescription';
   
  public function deleteByPatientTreatedByDoctorID($patientTreatedByDoctorID) {
    $query = QueryBuilder::delete($this->tbl, "`patients_treated_by_doctors_id` = '$patientTreatedByDoctorID'");
    return mysqli_query($GLOBALS['DB'], $query);
  }

  public function findFullPrescription($prescriptionId) {

        $sql = "
        SELECT 
            p.id AS prescription_id,
            p.created_at,
            p.patient_pb,
            p.patient_pr,
            p.patient_rr,
            p.patient_weight,
            p.doctor_diagnose,
            p.doctor_clinical_note,

            pt.code  AS patient_code,
            pt.first_name,
            pt.father_name,
            pt.last_name,
            pt.age

        FROM prescription p
        INNER JOIN patient pt ON pt.id = p.patient_id
        WHERE p.id = '$prescriptionId'
        LIMIT 1
        ";

        $result =  $this->raw($sql);

        return ($result && $result->num_rows > 0) ? mysqli_fetch_assoc($result) : null;
    }
}