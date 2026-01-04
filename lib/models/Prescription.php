<?php
class Prescription extends BaseModel{
  protected $tbl = 'prescription';

    /**
     * Get paginated prescription list with search & filters
     *
     * @param array $params
     *  - page
     *  - per_page
     *  - search
     *  - filter (all|today|this_week|last_week)
     *
     * @return array
     */
    public function getList(array $params): array
    {
        /* ===============================
         * Normalize Inputs
         * =============================== */
        $page     = max(1, (int)($params['page'] ?? 1));
        $perPage  = (int)($params['per_page'] ?? 10);
        $search   = trim($params['search'] ?? '');
        $filter   = $params['filter'] ?? 'all';

        $offset = ($page - 1) * $perPage;

        /* ===============================
         * WHERE Clause Builder
         * =============================== */
        $where = "WHERE 1=1";

        // ---- Search by patient code or name ----
        if ($search !== '') {
            $search = escape_data($search);
            $where .= " AND (
                patient.code LIKE '%$search%' OR
                CONCAT_WS(' ', patient.first_name, patient.last_name) LIKE '%$search%' OR
                CONCAT_WS(' ', patient.first_name, patient.father_name) LIKE '%$search%'
            )";
        }

        // ---- Date Filters ----
        switch ($filter) {
            case 'today':
                $where .= " AND DATE(prescription.created_at) = CURDATE()";
                break;

            case 'this_week':
                $where .= " AND YEARWEEK(prescription.created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;

            case 'last_week':
                $where .= " AND YEARWEEK(prescription.created_at, 1) = YEARWEEK(CURDATE(), 1) - 1";
                break;
            case 'this_month':
                $where .= " 
                    AND YEAR(prescription.created_at) = YEAR(CURDATE())
                    AND MONTH(prescription.created_at) = MONTH(CURDATE())
                ";
                break;
        }

        /* ===============================
         * Main Data Query
         * =============================== */
        $dataSql = "
            SELECT
                prescription.id AS prescription_id,
                prescription.created_at,
                patient.code AS patient_code,
                patient.first_name,
                patient.father_name,
                patient.last_name,
                patient.age
            FROM prescription
            INNER JOIN patient ON patient.id = prescription.patient_id
            $where
            GROUP BY prescription.id
            ORDER BY prescription.created_at DESC
            LIMIT $offset, $perPage
        ";

        $result = $this->raw($dataSql);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    }

    public function getById(int $prescriptionId): array
    {
        $prescriptionId = (int)$prescriptionId;

        $sql = "SELECT
                p.id AS prescription_id,
                p.created_at,
                p.patient_cc,
                p.patient_past_history,
                p.patient_pb,
                p.patient_pr,
                p.patient_rr,
                p.patient_weight,
                p.doctor_diagnose,
                p.doctor_clinical_note,

                pt.id AS patient_id,
                pt.code AS patient_code,
                pt.first_name,
                pt.father_name,
                pt.last_name,
                pt.age,

                CONCAT(
                    '[',
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'prescribed_id', pm.id,
                                'medicine_id', m.id,
                                'generic_name', m.generic_name,
                                'company_name', m.company_name,
                                'dose', m.dose,
                                'usage_description', m.usage_description,
                                'medicine_type', mt.name,
                                'medicine_category', mc.name,
                                'total_usage', pm.medicine_total_usage,
                                'usage_frequency', pm.medicine_usage_frequency,
                                'usage_form', pm.medicine_usage_form,
                                'doctor_note', pm.medicine_doctor_note
                            )
                            ORDER BY pm.id
                        ),
                    ']'
                ) AS medicines

            FROM prescription p
            INNER JOIN patient pt ON pt.id = p.patient_id
            LEFT JOIN prescribed_medicine pm ON pm.prescription_id = p.id
            LEFT JOIN medicine m ON m.id = pm.medicine_id
            LEFT JOIN medicine_type mt ON mt.id = m.medicine_type_id
            LEFT JOIN medicine_category mc ON mc.id = m.medicine_category_id

            WHERE p.id = $prescriptionId
            GROUP BY p.id
            LIMIT 1";

        $result = $this->raw($sql);
        return $result ? mysqli_fetch_assoc($result) : [];
    }

}
