<?php

class PatientPrescription extends BaseModel{
  protected $tbl = 'patient_prescription';
   
  public function deleteByPatientTreatedByDoctorID($patientTreatedByDoctorID) {
    $query = QueryBuilder::delete($this->tbl, "`patients_treated_by_doctors_id` = '$patientTreatedByDoctorID'");
    return mysqli_query($GLOBALS['DB'], $query);
  }
}