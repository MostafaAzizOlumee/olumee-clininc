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
}
