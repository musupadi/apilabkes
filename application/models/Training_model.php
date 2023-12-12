<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Training_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    
    public function getProfile($Code,$user_type)
    {
        $data = [];
        if($user_type=='participant'){
            $this->db->select('db_training.participant.*,db_research.university.Name_University');
            $this->db->from('db_training.participant');
            $this->db->join('db_research.university', 
            'db_training.participant.InstitutionID = db_research.university.ID','left');
            $this->db->where(array(
                'ParticipantCode' => $Code,
                'IsDeleted' => '0'
            ));
            $data = $this->db->get()->result_array();
        } else if($user_type=='trainer'){
            $this->db->select('db_training.trainer.*,db_research.university.Name_University');
            $this->db->from('db_training.trainer');
            $this->db->join('db_research.university', 
            'db_training.trainer.InstitutionID = db_research.university.ID','left');
            $this->db->where(array(
                'TrainerCode' => $Code,
                'IsDeleted' => '0'
            )); 
            $data = $this->db->get()->result_array();
        }

        return $data;

    }

    public function updateProfile($user_id,$dataUpdate)
    {
        $this->db->where('ParticipantCode', $user_id);
        $this->db->update('db_training.participant',$dataUpdate);
        return $this->db->affected_rows();

    }

    public function readlistPeriode($PeriodeID)
    {
        if($PeriodeID!=''){
            $dataPeriode = $this->db->order_by('ID','DESC')->get_where('db_training.periode',array(
                'ID' => $PeriodeID
            ))->result_array();
        } else {
            $dataPeriode = $this->db->order_by('ID','DESC')->get('db_training.periode')->result_array();
        }

        return $dataPeriode;
    }

    public function readlistActivePeriode($PeriodeID)
    {
        if($PeriodeID!=''){
            $dataPeriode = $this->db->order_by('ID','DESC')->get_where('db_training.periode',array(
                'ID' => $PeriodeID
            ))->result_array();
        } else {
            $dataPeriode = $this->db->order_by('ID','DESC')->get_where('db_training.periode',array(
                'IsDeleted' => '0'
            ))->result_array();
        }

        return $dataPeriode;
    }

    public function readTimetableParticipant($user_id,$PeriodeID)
    {
        // get all periode

        $dataPeriode = $this->readlistPeriode($PeriodeID);

        
        if(count($dataPeriode)>0){
            for ($i=0; $i < count($dataPeriode); $i++) { 
                $d = $dataPeriode[$i];
                // get timetable
                $datatimetable = $this->db->query('SELECT sp.TimetableID,
                CASE 
                WHEN DATE(p.ShowScoreTask) <= CURDATE() THEN sp.ScoreTask
                ELSE "" END AS ScoreTask,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScoreExam
                ELSE "" END AS ScoreExam,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScoreExam
                ELSE "" END AS ScoreExam,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScoreFinal
                ELSE "" END AS ScoreFinal,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScoreGrade
                ELSE "" END AS ScoreGrade,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScoreGradeValue
                ELSE "" END AS ScoreGradeValue,
                CASE 
                WHEN DATE(p.ShowScoreExam) <= CURDATE() THEN sp.ScorePoints
                ELSE "" END AS ScorePoints,
                p.ShowScoreTask,
                p.ShowScoreExam,
                c.Name AS Course, c.NameEng AS CourseEng, c.Credit, 
                t.TotalSession, t.IsWeekly, t.Group
                FROM db_training.study_plan sp 
                LEFT JOIN db_training.timetable t ON (t.ID = sp.TimetableID)
                LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
                LEFT JOIN db_training.periode p ON (p.ID = sp.PeriodeID)
                WHERE sp.ParticipantCode = "'.$user_id.'" AND sp.PeriodeID = "'.$d['ID'].'" ')->result_array();

                if(count($datatimetable)>0){
                    for ($t=0; $t < count($datatimetable); $t++) { 
                        $dt = $datatimetable[$t];
                        
                        // trainer
                        $datatimetable[$t]['dataTrainer'] = $this->db->query('SELECT t.IsCoordinator, 
                        TRIM(CONCAT(
                            COALESCE(tr.TitleAhead, NULL, "", "-" || tr.TitleAhead)
                            , " "
                            , tr.Name
                            , " "
                            , COALESCE(tr.TitleBehind, NULL, "", "-" || tr.TitleBehind))) AS Name
                        FROM db_training.timetable_trainer t 
                        LEFT JOIN db_training.trainer tr ON (tr.TrainerCode = t.TrainerCode)
                        WHERE t.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                        
                        // schedule
                        $datatimetable[$t]['dataSchedule'] = $this->db->query('SELECT 
                        CASE
                        WHEN NULLIF(td.Day, "") THEN d.NameEng
                        ELSE "" END
                        AS Day,
                        td.Date,
                        CONCAT(
                            SUBSTRING(td.TimeStart,1,5),
                            " ",
                            SUBSTRING(td.TimeEnd,1,5)
                            ) AS Time, 
                            td.IsOnline,
                            td.RoomID,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.timetable_details td 
                        LEFT JOIN db_training.m_classroom c ON (c.ID = td.RoomID)
                        LEFT JOIN db_training.day d ON (d.ID = td.Day)
                        WHERE td.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();

                        // exam schedule
                        $datatimetable[$t]['dataExamSchedule'] = $this->db->query('SELECT es.Date, 
                        CONCAT(
                            SUBSTRING(es.ExamStart,1,5),
                            " ",
                            SUBSTRING(es.ExamEnd,1,5)
                            ) AS Time,
                            es.IsOnline,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.exam_schedule_details esd 
                        LEFT JOIN db_training.exam_schedule es ON (es.ID = esd.ExamScheduleID)
                        LEFT JOIN db_training.m_classroom c ON (c.ID = es.RoomID)
                        WHERE esd.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                    }
                    
                }

                $dataPeriode[$i]['dataTimetable'] = $datatimetable;

            }
        }

        return $dataPeriode;
    }

    public function readTimetableTrainer($user_id,$PeriodeID)
    {
        // get all periode
        $dataPeriode = $this->readlistPeriode($PeriodeID);
        if(count($dataPeriode)>0){
            for ($i=0; $i < count($dataPeriode); $i++) {
                $d = $dataPeriode[$i];

                $datatimetable = $this->db->query('SELECT t.*, tt.TimetableID, tt.IsCoordinator, c.Name AS Course, c.NameEng AS CourseEng, c.Credit
                FROM db_training.timetable_trainer tt
                LEFT JOIN  db_training.timetable t ON (t.ID = tt.TimetableID)
                LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
                WHERE t.PeriodeID = "'.$d['ID'].'" AND tt.TrainerCode = "'.$user_id.'" AND t.IsDeleted = "0"')->result_array();

                if(count($datatimetable)>0){
                    for ($t=0; $t < count($datatimetable); $t++) { 
                        $dt = $datatimetable[$t];
                        // trainer
                        $datatimetable[$t]['dataTrainer'] = $this->db->query('SELECT t.IsCoordinator, 
                        TRIM(CONCAT(
                            COALESCE(tr.TitleAhead, NULL, "", "-" || tr.TitleAhead)
                            , " "
                            , tr.Name
                            , " "
                            , COALESCE(tr.TitleBehind, NULL, "", "-" || tr.TitleBehind))) AS Name, tr.TrainerCode
                        FROM db_training.timetable_trainer t 
                        LEFT JOIN db_training.trainer tr ON (tr.TrainerCode = t.TrainerCode)
                        WHERE t.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                        
                        // schedule
                        $datatimetable[$t]['dataSchedule'] = $this->db->query('SELECT 
                        CASE
                        WHEN NULLIF(td.Day, "") THEN d.NameEng
                        ELSE "" END
                        AS Day,
                        td.Date,
                        CONCAT(
                            SUBSTRING(td.TimeStart,1,5),
                            " ",
                            SUBSTRING(td.TimeEnd,1,5)
                            ) AS Time, 
                            td.IsOnline,
                            td.RoomID,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.timetable_details td 
                        LEFT JOIN db_training.m_classroom c ON (c.ID = td.RoomID)
                        LEFT JOIN db_training.day d ON (d.ID = td.Day)
                        WHERE td.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();

                        // exam schedule
                        $datatimetable[$t]['dataExamSchedule'] = $this->db->query('SELECT es.Date, 
                        CONCAT(
                            SUBSTRING(es.ExamStart,1,5),
                            " ",
                            SUBSTRING(es.ExamEnd,1,5)
                            ) AS Time,
                            es.IsOnline,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.exam_schedule_details esd 
                        LEFT JOIN db_training.exam_schedule es ON (es.ID = esd.ExamScheduleID)
                        LEFT JOIN db_training.m_classroom c ON (c.ID = es.RoomID)
                        WHERE esd.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                    }

                }

                $dataPeriode[$i]['dataTimetable'] = $datatimetable;

            }
        }

        return $dataPeriode;
    }

    public function updateScore($ID,$dataUpdate)
    {
        // get grade

        $nexttoupdate = true;

        if(
            $dataUpdate['ScoreTask']!='' && $dataUpdate['ScoreTask']!=null &&
            $dataUpdate['ScoreExam']!='' && $dataUpdate['ScoreExam']!=null
            ){

                $dataConf = $this->db->query('SELECT sp.PeriodeID, sp.TimetableID, 
                t.PercentageTask, t.PercentageExam, c.Credit
                FROM db_training.study_plan sp
                LEFT JOIN db_training.timetable t ON (sp.TimetableID = t.ID)
                LEFT JOIN db_training.m_course c ON (t.CourseID = c.ID)
                WHERE sp.ID = "'.$ID.'" ')->result_array();

                $PercentageTask = (float) $dataConf[0]['PercentageTask'];
                $PercentageExam = (float) $dataConf[0]['PercentageExam'];

                $ScoreTask = (float) $dataUpdate['ScoreTask']; 
                $avg_task = ($ScoreTask>0) ? $ScoreTask * ($PercentageTask / 100) : 0;

                $ScoreExam = (float) $dataUpdate['ScoreExam']; 
                $avg_exam = ($ScoreExam>0) ? $ScoreExam * ($PercentageExam / 100) : 0;

                $ScoreFinal = $avg_task + $avg_exam;

                $dataGrade = $this->db->query('SELECT * FROM db_training.grade 
                WHERE PeriodeID = "'.$dataConf[0]['PeriodeID'].'"
                AND (RangeStart <= "'.$ScoreFinal.'" AND RangeEnd >= "'.$ScoreFinal.'") ')->result_array();

                if(count($dataGrade)<=0){
                    $nexttoupdate = false;
                } else {
                    $dataUpdate['ScoreFinal'] = $ScoreFinal;
                    $dataUpdate['ScoreGrade'] = $dataGrade[0]['Grade'];
                    $dataUpdate['ScoreGradeValue'] = $dataGrade[0]['Score'];
                    $dataUpdate['ScorePoints'] = $dataGrade[0]['Score'] * $dataConf[0]['Credit'] ;
                }

                // get percentage
        } else {
            $dataUpdate['ScoreFinal'] = null;
                    $dataUpdate['ScoreGrade'] = null;
                    $dataUpdate['ScorePoints'] = null;
        }

        if($nexttoupdate){
            $this->db->where('ID', $ID);
            $this->db->update('db_training.study_plan',$dataUpdate);
        }

        return $nexttoupdate;
        
    }

    public function readPayment($user_id)
    {
        $data = $this->db->query('SELECT py.*, p.Label FROM db_training.payment py 
        LEFT JOIN db_training.periode p ON (p.ID = py.PeriodeId)
        WHERE py.ParticipantCode = "'.$user_id.'" ORDER BY p.ID DESC')->result_array();

        return $data;
    }



    public function readListForum($user_id,$user_type,$TimetableID)
    {
        $data = [];
        if($user_type=='trainer'){
            $data = $this->db->query('SELECT f.*, p.Label AS PeriodeLabel, tb.PeriodeID,
            (SELECT COUNT(*) FROM db_training.forum_details fd WHERE fd.ForumID = f.ID) AS Total,
            tb.Group, c.Name AS Course, c.NameEng AS CourseEng FROM db_training.forum f 
            LEFT JOIN db_training.timetable_trainer t ON (t.TimetableID = f.TimetableID)
            LEFT JOIN db_training.timetable tb ON (tb.ID = t.TimetableID)
            LEFT JOIN db_training.periode p ON (tb.PeriodeID = p.ID)
            LEFT JOIN db_training.m_course c ON (c.ID = tb.CourseID)
            WHERE t.TrainerCode = "'.$user_id.'"
            ORDER BY f.CreatedAt DESC')->result_array();
        } else if ($user_type=='participant'){
            $dataTimetable = $this->db->select('TotalSession')->get_where('db_training.timetable',
            array(
                'ID' => $TimetableID
            ))->result_array();

            if(count($dataTimetable)>0){
                $TotalSession = (int) $dataTimetable[0]['TotalSession'];
                $dataForum = [];
                for ($i=1; $i <= $TotalSession ; $i++) { 
                    $d_f = $this->getForumByTimetanleAndSession($TimetableID,$i);
                    array_push($dataForum,$d_f);
                }

                $data = $dataForum;
            }

            
        }

        return $data;
    }

    private function getForumByTimetanleAndSession($TimetableID,$Session)
    {
        $data = $this->db->query('SELECT f.ID, f.Topic,
        (SELECT COUNT(*) FROM db_training.forum_details fd WHERE fd.ForumID = f.ID) AS Total
        FROM db_training.forum f 
        WHERE f.TimetableID = "'.$TimetableID.'" 
        AND f.Session = "'.$Session.'"')->result_array();

        $result = array(
            'Session' => $Session,
            'ForumID' => (count($data)>0) ? $data[0]['ID'] : '',
            'Topic' => (count($data)>0) ? $data[0]['Topic'] : '',
            'Total' => (count($data)>0) ? $data[0]['Total'] : ''
        );

        return $result;

    }

    public function forumCreateAction($user_id,$dataInsert)
    {
        // cek apakah sesi sudah pernah dibuat atau blm
        $dataCheck = $this->db->get_where('db_training.forum',array(
            'TimetableID' => $dataInsert['TimetableID'],
            'Session' => $dataInsert['Session']
        ))->result_array();

        if(count($dataCheck)<=0){
            $dataInsert['CreatedBy'] = $user_id;
            $this->db->insert('db_training.forum',$dataInsert);
            $ForumID = $this->db->insert_id();
            $result = array('Status' => 1, 'ForumID' => $ForumID);
        } else {
            $result = array('Status' => 0, 'ForumID' => $dataCheck[0]['ID'], 'DataCheck' => $dataCheck);
        }

        return $result;
    }

    public function enterForumComment($dataInsert)
    {
        $this->db->insert('db_training.forum_details',$dataInsert);
        return $this->db->insert_id();
    }

    public function readForumDetails($user_id,$user_type,$ForumID)
    {
        $data = $this->db->query('SELECT f.*, c.NameEng AS CourseEng,
        c.Name AS Course, c.CourseCode, t.Group
        FROM db_training.forum f 
        LEFT JOIN db_training.timetable t ON (t.ID = f.TimetableID)
        LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
        WHERE f.ID = "'.$ForumID.'"')->result_array();


        if(count($data)>0){
            $d = $data[0];
            $checkUser = false;
            if($user_type=='trainer'){
            
                $datacheck = $this->db->get_where('db_training.timetable_trainer',
                array('TimetableID' => $d['TimetableID'], 'TrainerCode' => $user_id))->result_array();

                
                $checkUser = (count($datacheck)>0) ? true : false;
            } else if($user_type=='participant'){

                // cek apaka user allow
                $datacheck = $this->db->get_where('db_training.study_plan',
                array('TimetableID' => $d['TimetableID'], 'ParticipantCode' => $user_id))->result_array();

                $checkUser = (count($datacheck)>0) ? true : false;
            }

            if($checkUser){
                $data[0]['dataDetails'] = $this->getForumDetails($ForumID);
            } else {
                $data = [];
            }
            
        }

        return $data;
    }

    public function readForumDetailByFilter($TimetableID,$Session)
    {
        $data = $this->db->get_where('db_training.forum',
        array(
            'TimetableID' => $TimetableID,
            'Session' => $Session
        ))->result_array();
        return $data;
    }

    private function getForumDetails($ForumID)
    {
        $data = $this->db->query('SELECT fd.*,
                CASE
                WHEN NULLIF(fd.TrainerCode, "") 
                THEN 
                    TRIM(CONCAT(
                        COALESCE(t.TitleAhead, NULL, "", "-" || t.TitleAhead)
                        , " ", t.Name, " "
                        , COALESCE(t.TitleBehind, NULL, "", "-" || t.TitleBehind)))
                ELSE p.Name END AS UserName,

                m.Label AS MediaLabel,
                m.FileName AS MediaFileName,
                m.Extension AS MediaExtension,
                m.UploadedBy AS MediaUploadedBy,

                CASE
                WHEN NULLIF(fd.TrainerCode, "") THEN fd.TrainerCode
                ELSE p.ParticipantCode END AS UserCode,

                CASE
                WHEN NULLIF(fd2.TrainerCode, "") 
                THEN 
                    TRIM(CONCAT(
                        COALESCE(t2.TitleAhead, NULL, "", "-" || t2.TitleAhead)
                        , " ", t2.Name, " "
                        , COALESCE(t2.TitleBehind, NULL, "", "-" || t2.TitleBehind)))
                ELSE p2.Name END AS Q_UserName,
                CASE
                WHEN NULLIF(fd2.TrainerCode, "") THEN fd2.TrainerCode
                ELSE p2.ParticipantCode END AS Q_UserCode,

                fd2.TrainerCode AS Q_TrainerCode,
                fd2.ParticipantCode AS Q_ParticipantCode,
                fd2.Descriptions AS Q_Descriptions,
                fd2.MediaID AS Q_MediaID,
                fd2.CreatedAt AS Q_CreatedAt,

                m2.Label AS Q_MediaLabel,
                m2.FileName AS Q_MediaFileName,
                m2.Extension AS Q_MediaExtension,
                m2.UploadedBy AS Q_MediaUploadedBy

                FROM db_training.forum_details fd 
                LEFT JOIN db_training.trainer t ON  (fd.TrainerCode = t.TrainerCode)
                LEFT JOIN db_training.participant p ON (fd.ParticipantCode = p.ParticipantCode)
                LEFT JOIN db_training.media m ON (fd.MediaID = m.ID)

                LEFT JOIN db_training.forum_details fd2 ON (fd2.ID = fd.ForumDetailID) 
                LEFT JOIN db_training.trainer t2 ON  (fd2.TrainerCode = t2.TrainerCode)
                LEFT JOIN db_training.participant p2 ON (fd2.ParticipantCode = p2.ParticipantCode)
                LEFT JOIN db_training.media m2 ON (fd2.MediaID = m2.ID)
                WHERE fd.ForumID = "'.$ForumID.'"  ORDER BY fd.CreatedAt ASC ')->result_array();

        return $data;
    }

    public function readMedia($user_id)
    {
        $data = $this->db->order_by('UploadedAt','DESC')
        ->get_where('db_training.media',
        array('UploadedBy' => $user_id))->result_array();
        return $data;
    }

    public function addMedia($dataIns)
    {
        $this->db->insert('db_training.media',$dataIns);
        return 1;
    }

    public function addSummernoteMedia($dataIns)
    {
        $this->db->insert('db_training.summernote_image',$dataIns);
        return 1;
    }

    public function removeSummernoteMedia($file_name)
    {
        // $this->db->insert('db_training.summernote_image',$dataIns);
        $this->db->where('Image',$file_name);
        $this->db->delete('db_training.summernote_image');
        return 1;
    }

    public function readMaterial($TimetableID)
    {
        $data = $this->db->get_where('db_training.timetable',
        array(
            'ID' => $TimetableID
        ))->result_array();

        if(count($data)>0){
            $TotalSession = $data[0]['TotalSession'];
            $dataMaterial = [];
            for ($i=1; $i <= $TotalSession ; $i++) { 
                $dm = $this->getMaterial($TimetableID,$i);
                // print_r($dm)
                $arrP = array(
                    'Session' => $i,
                    'Material' => $dm
                );
                array_push($dataMaterial ,$arrP);
            }
            $data[0]['DataMaterial'] = $dataMaterial;
        }

        return $data;
    }

    public function getMaterial($TimetableID,$Session)
    {
        $data = $this->db->query('SELECT m.Descriptions,
        m.CreatedAt,
        m.CreatedBy,
        m.UpdatedAt,
        m.UpdatedBy,
        TRIM(CONCAT(
            COALESCE(tr.TitleAhead, NULL, "", "-" || tr.TitleAhead)
            , " "
            , tr.Name
            , " "
            , COALESCE(tr.TitleBehind, NULL, "", "-" || tr.TitleBehind))) AS UpdatedByName,
        m.MediaID,
        
        med.Label AS MediaLabel,
        med.FileName AS MediaFileName,
        med.Extension AS MediaExtension,
        med.UploadedBy AS MediaUploadedBy

        FROM db_training.material m 
        LEFT JOIN db_training.media med on (med.ID = m.MediaID)
        LEFT JOIN db_training.trainer tr ON (tr.TrainerCode = m.UpdatedBy)
        WHERE m.TimetableID = "'.$TimetableID.'" AND m.Session = "'.$Session.'"')->result_array();

        return $data;
    }

    public function updateMaterial($user_id,$dataUpdate)
    {
        // cek apakah sudah ada atau blm
        $TimetableID = $dataUpdate['TimetableID'];
        $Session = $dataUpdate['Session'];

        $dataCK = $this->db->get_where('db_training.material',array(
            'TimetableID' => $TimetableID,
            'Session' => $Session
        ))->result_array();

        if(count($dataCK)>0){
            // update
            $dataUpdate['UpdatedBy'] = $user_id;
            $dataUpdate['UpdatedAt'] = date('Y-m-d H:i:s');

            $ID = $dataCK[0]['ID'];
            $this->db->where('ID', $ID);
            $this->db->update('db_training.material',$dataUpdate);
            // return $this->db->affected_rows();

        } else {
            // insert
            $dataUpdate['CreatedBy'] = $user_id;
            $dataUpdate['UpdatedBy'] = $user_id;
            $dataUpdate['UpdatedAt'] = date('Y-m-d H:i:s');

            $this->db->insert('db_training.material',$dataUpdate);
        }

        return 1;

    }

    public function getSummernoteMediaByID($SummernoteID)
    {
        $data = $this->db->get_where('db_training.summernote_image',
        array('SummernoteID' => $SummernoteID))->result_array();

        return $data;
    }

    public function readQUestionType()
    {
        $data = $this->db->order_by('Ordered','ASC')
        ->get_where('db_training.q_question_type',
        array('IsShow' => '1'))->result_array();
        return $data;
    }

    public function createQuestion($user_id,$dataQuestion)
    {
        
        $insQuestion = array(
            'QTID' => $dataQuestion['QTID'],
            'SummernoteID' => $dataQuestion['SummernoteID'],
            'Question' => $dataQuestion['Question'],
            'Note' => $dataQuestion['Note'],
            'CreatedAt' => date('Y-m-d H:i:s'),
            'CreatedBy' => $user_id,
        );
        
        $this->db->insert('db_training.q_question',$insQuestion);
        $QuestionID = $this->db->insert_id();
        // update summernote image
        $this->updateSummernoteImage($dataQuestion['SummernoteID']);

        // add option
        if(count($dataQuestion['dataOptions'])>0){
            for ($i=0; $i < count($dataQuestion['dataOptions']); $i++) { 
                $d = (array) $dataQuestion['dataOptions'][$i];

                $insOpt = array(
                    'QuestionID' => $QuestionID,
                    'SummernoteID' => $d['SummernoteID'],
                    'Option' => $d['Option'],
                    'IsTheAnswer' => $d['IsTheAnswer'],
                    'Point' => $d['Point']
                );
                $this->db->insert('db_training.q_question_options',$insOpt);
                // update summernote image
                $this->updateSummernoteImage($d['SummernoteID']);
            }
        }

        return 1;
    }

    public function readQuestionList($user_id)
    {
        $data = $this->db->query('SELECT q.*, qt.Label AS QTLabel, qt.Descriptions AS QTDescriptions 
                FROM db_training.q_question q 
                LEFT JOIN db_training.q_question_type qt ON (qt.ID = q.QTID)
                WHERE q.CreatedBy = "'.$user_id.'" 
                AND q.IsDeleted = "0" ORDER BY q.CreatedAt DESC')->result_array();

        if(count($data)>0){
            for ($i=0; $i < count($data); $i++) { 

                $dataOpstions = [];
                if($data[$i]['QTID']=='5' || $data[$i]['QTID']=='6'){
                    $dataOpstions = $this->db
                    ->get_where('db_training.q_question_options',
                    array(
                        'QuestionID' => $data[$i]['ID']
                        ))->result_array();
                }

                $data[$i]['dataOpstions'] = $dataOpstions;
            }
        }

        return $data;
    }

    public function createQuiz($user_id,$dataQuiz)
    {
        $whereCk = array(
                'PeriodeID' => $dataQuiz['PeriodeID'],
                'TimetableID' => $dataQuiz['TimetableID'],
                'Session' => $dataQuiz['Session'],
                'IsForExam' => $dataQuiz['IsForExam'],
            );

        if($dataQuiz['IsForExam']=='1'){
            unset($whereCk['Session']);
        }

        $cekQuiz = $this->db
        ->get_where('db_training.q_quiz',$whereCk)->result_array();

        if(count($cekQuiz)<=0){

            $quizIns = array(
                'PeriodeID' => $dataQuiz['PeriodeID'],
                'TimetableID' => $dataQuiz['TimetableID'],
                'Session' => $dataQuiz['Session'],
                'Duration' => $dataQuiz['Duration'],
                'IsForExam' => $dataQuiz['IsForExam'],
                'Note' => $dataQuiz['Note'],
                'CreatedBy' => $user_id
            );

            $data = $this->db->insert('db_training.q_quiz',$quizIns);
            $QuizID = $this->db->insert_id();

            $Details = json_decode($dataQuiz['Details']);

            if(count($Details)>0){
                for ($i=0; $i < count($Details); $i++) { 
                    $d = (array) $Details[$i];
                    $qqIns = array(
                        'QuizID' => $QuizID,
                        'QuestionID' => $d['ID'],
                        'Point' => $d['Point']
                    );

                    $data = $this->db->insert('db_training.q_quiz_details',$qqIns);
                }
            }

            $result = 1;

        } else {
            $result = 0;
        }

        

        return $result;


    }

    public function getListModule($TimetableID)
    {
        $dataSession = $this->getSessionDate($TimetableID);
        // cek modul
        for ($i=0; $i < count($dataSession); $i++) { 
            $sesi = $i + 1;
            $dataWhere = array(
                'TimetableID' => $TimetableID,
                'Session' => $sesi
                );

            $Count_Quiz = $this->db->from('db_training.q_quiz')
            ->where($dataWhere)->count_all_results();

            $Count_Forum = $this->db->from('db_training.forum')
            ->where($dataWhere)->count_all_results();

            $Count_Forum_Details = $this->db->query('SELECT COUNT(*) AS Total
                                FROM db_training.forum_details fd
                                LEFT JOIN db_training.forum f ON (f.ID = fd.ForumID)
                                WHERE f.TimetableID = "'.$TimetableID.'" AND f.Session = "'.$sesi.'"')->result_array()[0]['Total'];

            $Count_Material = $this->db->from('db_training.material')
            ->where($dataWhere)->count_all_results();
            
            $dataSession[$i]['Session'] = $sesi;
            $dataSession[$i]['Count_Quiz'] = $Count_Quiz;
            $dataSession[$i]['Count_Forum'] = $Count_Forum;
            $dataSession[$i]['Count_Forum_Details'] = $Count_Forum_Details;
            $dataSession[$i]['Count_Material'] = $Count_Material;
                
        }
        
        return $dataSession;
    }

    private function getSessionDate($TimetableID)
    {
        $data = $this->db->query('SELECT t.*, p.PeriodeStart, p.PeriodeEnd 
        FROM db_training.timetable t 
        LEFT JOIN db_training.periode p ON (p.ID = t.PeriodeID) 
        WHERE t.ID = "'.$TimetableID.'" ')->result_array();

        if(count($data)>0){
            $d = $data[0];
            $dataSesi = $this->db->query('SELECT d.*, c.Name AS RoomName FROM db_training.timetable_details d 
            LEFT JOIN db_training.m_classroom c ON (c.ID = d.RoomID)
            WHERE d.TimetableID = "'.$TimetableID.'"')->result_array();

            if($d['IsWeekly']=='1'){
                $firsDate = $this->getFirstDatelearningOnline($d['PeriodeStart'],$dataSesi[0]['Day']);
                $dataDate = [];
                for ($i=0; $i < $d['TotalSession'] ; $i++) { 
                    $date = date("Y-m-d", strtotime($firsDate . " +".$i." Week"));
                    $date_end = date("Y-m-d", strtotime($date . " +6 days"));
                    
                    $arr = array(
                        "TimetableID" => $TimetableID,
                        "Day" => $dataSesi[0]['Day'],
                        "Date" => $date,
                        "DateEnd" => $date_end,
                        "TimeStart" => $dataSesi[0]['TimeStart'],
                        "TimeEnd" => $dataSesi[0]['TimeEnd'],
                        "IsOnline" => $dataSesi[0]['IsOnline'],
                        "RoomID" => $dataSesi[0]['RoomID']
                    );

                    // $firsDate = $date;
                    array_push($dataDate,$arr);
                }

                $dataSesi = $dataDate;
            }

            return $dataSesi;

        }

        

    }

    public function getFirstDatelearningOnline($StartDate, $DayNumber)
    {
        $result = '';
        for ($i = 0; $i <= 7; $i++) {
            $dtNow = date("Y-m-d", strtotime($StartDate . " +" . $i . " days"));
            $dtNumber = date("N", strtotime($StartDate . " +" . $i . " days"));
            if ($DayNumber == $dtNumber) {
                $result = $dtNow;
                break;
            }
        }
        return $result;
    }

    private function updateSummernoteImage($SummernoteID)
    {
        $this->db->where('SummernoteID', $SummernoteID);
        $this->db->update('db_training.summernote_image',array(
            'Status' => '1'
        ));

        return 1;
    }


    public function readQuizDetails($user_id,$TimetableID,$Session,$IsForExam)
    {

        // $dataCk = $this->db->get_where('db_training.study_plan',
        // )->result_array();

        $dataCk = $this->db->from('db_training.study_plan')
            ->where(array(
            'TimetableID' => $TimetableID,
            'ParticipantCode' => $user_id
        ))->count_all_results();

        if($dataCk>0){

            $whereSesi = ($IsForExam=='0') ? ' AND q.Session = "'.$Session.'"' : '';

            $data = $this->db->query('SELECT q.*, p.Label, t.Group, c.Name AS CourseName, 
                c.NameEng AS CourseNameEng, es.Date AS ExamDate, es.ExamStart, es.ExamEnd, es.IsOnline AS ExamIsOnline,
                qqa.ID AS QuizAnsID, qqa.Date AS qa_Date, qqa.TimeStart AS qa_TimeStart, qqa.TimeEnd AS qa_TimeEnd,
                qqa.SubmitedAt AS qa_SubmitedAt, qqa.WorkingDuration AS qa_WorkingDuration
                FROM db_training.q_quiz q 
                LEFT JOIN db_training.periode p ON (p.ID = q.PeriodeID)
                LEFT JOIN db_training.timetable t ON (t.ID = q.TimetableID)
                LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
                LEFT JOIN db_training.exam_schedule_details esd ON (esd.TimetableID = t.ID)
                LEFT JOIN db_training.exam_schedule es ON (es.ID = esd.ExamScheduleID)
                LEFT JOIN db_training.q_quiz_ans qqa ON (qqa.QuizID = q.ID AND qqa.ParticipantCode = "'.$user_id.'" )
                WHERE q.TimetableID = "'.$TimetableID.'" AND q.IsForExam = "'.$IsForExam.'" '.$whereSesi)->result_array();
            
            $result = array('status'=>1,'data' => $data);
        } else {
            $result = array('status'=>0);
        }

        

        return $result;
    }

    public function setQuizStart($user_id,$IsForExam,$QuizID)
    {

        // cek apakah sudah ada atau blm
        $result = $this->db->get_where('db_training.q_quiz_ans',array(
            'ParticipantCode' => $user_id,
            'QuizID' => $QuizID
        ))->result_array();

        if(count($result)<=0) {
            // get durasi
            $Duration = 0;
            if($IsForExam=='0'){

                $Duration = $this->db->get_where('db_training.q_quiz',array(
                    'ID' => $QuizID
                ))->result_array()[0]['Duration'];

            } else {

                // duration
                $dataD = $this->db->query('SELECT es.Date, es.ExamStart, es.ExamEnd FROM db_training.q_quiz q
                LEFT JOIN db_training.exam_schedule_details esd ON (esd.TimetableID = q.TimetableID)
                LEFT JOIN db_training.exam_schedule es ON (es.ID = esd.ExamScheduleID)
                WHERE q.ID = "'.$QuizID.'" LIMIT 1 ')->result_array();
                if(count($dataD)>0){
                    $d = $dataD[0];
                    // Declare two dates
                    $date1 = strtotime($d['Date']." ".$d['ExamStart']);
                    $date2 = strtotime($d['Date']." ".$d['ExamEnd']);
                    
                    // Formulate the Difference between two dates
                    $diff = abs($date2 - $date1);

                    $minutes = $diff/60;

                    $Duration = ceil($minutes);
                }

            }

            $arrIns = array(
                'ParticipantCode' => $user_id,
                'QuizID' => $QuizID,
                'Duration' => $Duration,
                'Date' => date("Y-m-d"),
                'TimeStart' => date("H:i:s"),
                'TimeEnd' => date("H:i:s", strtotime("+".$Duration." minutes"))
            );

            $this->db->insert('db_training.q_quiz_ans',$arrIns);

            $id = $this->db->insert_id();
            $result = $this->db->get_where('db_training.q_quiz_ans',array('ID' => $id))->result_array();
        }

        if(count($result)>0){
            
            $dateQuizEnd = strtotime($result[0]['Date']." ".$result[0]['TimeEnd']);
            $minutes = ceil(($dateQuizEnd - time()) / 60);
            $result[0]['remaining_time'] = ($minutes>0) ? $minutes : 0;

            $checkAnswer = ($result[0]['SubmitedAt']!='' && $result[0]['SubmitedAt']!=null) ? 1 : 0;
            $result[0]['dataQuestion'] = $this->showQuestionInQuiz($user_id,$QuizID,$checkAnswer,1);

            $showPoint = true;
            $totalPoint = 0;

            // count score
            if($result[0]['SubmitedAt']!='' && $result[0]['SubmitedAt']!=null){
                
                for ($i=0; $i < count($result[0]['dataQuestion']) ; $i++) { 
                    $d = $result[0]['dataQuestion'][$i];
                    if($d['PointAswer']!='' && $d['PointAswer']!=null){
                        $totalPoint = $totalPoint + $d['PointAswer'];
                    } else {
                        $showPoint = false;
                    }
                }

            } else {
                $showPoint = false;
            }

            $result[0]['showPoint'] = $showPoint;
            $result[0]['PointTotal'] = $totalPoint;
        }

        return $result;
        
    }

    private function showQuestionInQuiz($user_id,$QuizID,$checkAnswer,$random)
    {
        $OrderRandom = ($random==1) ? ' ORDER BY RAND()' : '';
        $data = $this->db->query('SELECT qd.*, q.Question,  q.QTID, qt.Label AS QuestionType,
                qad.Point AS PointAswer, qad.EssayAnswer, qad.ID AS QuizAnsDetailID
                FROM db_training.q_quiz_details qd 
                LEFT JOIN db_training.q_question q ON (q.ID = qd.QuestionID)
                LEFT JOIN db_training.q_question_type qt ON (qt.ID = q.QTID)
                LEFT JOIN db_training.q_quiz_ans_details qad ON (qad.QuizID = qd.QuizID 
                AND qad.QuestionID = qd.QuestionID AND qad.ParticipantCode = "'.$user_id.'")
                WHERE qd.QuizID = "'.$QuizID.'" ')->result_array();

        if(count($data)>0){
            $OrderRandom = ($random==1) ? 'RANDOM' : 'ASC';
            for ($i=0; $i < count($data); $i++) { 
                $data[$i]['dataOption'] = $this->db->select('ID,Option')->order_by('ID', $OrderRandom)->get_where('db_training.q_question_options',
                array(
                    'QuestionID' => $data[$i]['QuestionID']
                ))->result_array();

                $dataOptionAnswer = [];
                $dataOptionAnswerArr = [];
                if($checkAnswer==1 && $data[$i]['QuizAnsDetailID']!='' && $data[$i]['QuizAnsDetailID']!=null){
                    $dataOptionAnswer = $this->db->get_where('db_training.q_quiz_ans_detail_options',
                    array(
                        'QuizAnsDetailID' => $data[$i]['QuizAnsDetailID']
                    ))->result_array();

                    if(count($dataOptionAnswer)>0){
                        for ($a=0; $a < count($dataOptionAnswer); $a++) { 
                            $QuestionOptionID = $dataOptionAnswer[$a]['QuestionOptionID'];
                            array_push($dataOptionAnswerArr,$QuestionOptionID);
                        }
                    }
                }

                

                $data[$i]['dataOptionAnswer'] = $dataOptionAnswer;
                $data[$i]['dataOptionAnswerArr'] = $dataOptionAnswerArr;
            }
        }


        return $data;
    }

    public function setAnswer($QuizAnsID,$dataAnswer)
    {
        // Cek waktu pengerjaan
        $dataQuiz = $this->db
        ->get_where('db_training.q_quiz_ans',
        array('ID' => $QuizAnsID))->result_array();

        $QuizID = 0;

        // cek apakah sudah pernah isi atau blm
        $dataAnsCk = $this->db->get_where('db_training.q_quiz_ans_details',
        array('QuizAnsID' => $QuizAnsID))->result_array();

        $status = 0;

        if(count($dataQuiz)>0 && count($dataAnsCk)<=0 && count($dataAnswer)>0){
            // cek tanggal
            $date = $dataQuiz[0]['Date'];
            $dateNow = new DateTime(date('Y-m-d H:i:s'));
            $dateStart = new DateTime($date.' '.$dataQuiz[0]['TimeStart']);
            $dateEnd = new DateTime($date.' '.$dataQuiz[0]['TimeEnd']);

            if($dateNow >=  $dateStart && $dateNow<=$dateEnd){
                $status = 1;
                for ($i=0; $i < count($dataAnswer); $i++) { 
                    $dataIns = (array) $dataAnswer[$i];
                    $ansOption = (array) $dataIns['ansOption'];
                    unset($dataIns['ansOption']);

                    $this->db->insert('db_training.q_quiz_ans_details',$dataIns);
                    $QuizAnsDetailID = $this->db->insert_id();

                    // get type question
                    $QTID = $this->db->select('QTID')->get_where('db_training.q_question',array(
                        'ID' => $dataIns['QuestionID']
                    ))->result_array()[0]['QTID'];

                    $TotapPoint = $this->db->select('Point')->get_where('db_training.q_quiz_details',
                    array(
                        'QuizID' => $dataIns['QuizID'],
                        'QuestionID' => $dataIns['QuestionID']
                    ))->result_array()[0]['Point'];

                    $QuizID = $dataIns['QuizID'];

                    if(count($ansOption)){
                        $PointMultiple = 0;
                        $PointSemestera = 0;
                        for ($opt=0; $opt < count($ansOption); $opt++) { 
                            $QuestionOptionID = $ansOption[$opt];
                            $dataAnsOpt = $this->db->get_where('db_training.q_question_options'
                                            ,array('ID' => $QuestionOptionID))->result_array();
                            if(count($dataAnsOpt)>0){
                                $dataInsOpt = array(
                                    'QuizAnsDetailID' => $QuizAnsDetailID,
                                    'QuestionOptionID' => $QuestionOptionID,
                                    'PointOption' => $dataAnsOpt[0]['Point'],
                                    'IsCorrect' => $dataAnsOpt[0]['IsTheAnswer']
                                );

                                // ini untuk multi select
                                $PointSemestera = $PointSemestera + $dataAnsOpt[0]['Point'];

                                // ini untuk single select
                                $PointMultiple = ($dataAnsOpt[0]['IsTheAnswer']=='1') ? $TotapPoint : 0;

                                $this->db->insert('db_training.q_quiz_ans_detail_options',$dataInsOpt);

                                // $PointMultiple = 
                            }
                            
                        }

                        if($QTID==6){
                            if($PointSemestera>0){
                                $PointMultiple = $TotapPoint * ($PointSemestera /100);
                            } else {
                                $PointMultiple = 0;
                            }
                        }

                        // update point
                        $this->db->where('ID', $QuizAnsDetailID);
                        $this->db->update('db_training.q_quiz_ans_details',array(
                            'Point' => $PointMultiple
                        ));
                    }                    

                }

                // update submited at
                $SubmitedAt = date('Y-m-d H:i:s');
                $minutes = abs(strtotime($date.' '.$dataQuiz[0]['TimeStart']) - time()) / 60;
                $WorkingDuration = $minutes;
                $this->db->where('ID', $QuizAnsID);
                $this->db->update('db_training.q_quiz_ans',array(
                    'SubmitedAt' => $SubmitedAt,
                    'WorkingDuration' => $WorkingDuration
                ));
                // print_r($dataAnswer);
            }
        }

        return array('status' => $status,'QuizID' => $QuizID);
    }

    public function readQuizExam($TimetableID)
    {
        $data = $this->db->get_where('db_training.q_quiz',array(
            'TimetableID' => $TimetableID,
            'IsForExam' => '1'
        ))->result_array();

        $dataJadwalExam = $this->db->query('SELECT es.*, c.Name AS RoomName FROM db_training.exam_schedule_details esd 
        LEFT JOIN db_training.exam_schedule es ON (esd.ExamScheduleID = es.ID)
        LEFT JOIN db_training.m_classroom c ON (c.ID = es.RoomID)
        WHERE esd.TimetableID = "'.$TimetableID.'" LIMIT 1')->result_array();

        $result = array(
            'Exam' => $data,
            'Schedule' => $dataJadwalExam
        );

        return $result;

    }

    public function getListTrainerScore($user_id, $PeriodeID, $TimetableID)
    {
        // get Score trainer
        $dataPeriode = $this->readlistPeriode($PeriodeID);
        if(count($dataPeriode)>0){
            for ($i=0; $i < count($dataPeriode); $i++) {
                $d = $dataPeriode[$i];
                $datatimetable = $this->db->query('SELECT sp.ID, sp.TimetableID,
                sp.ScoreTask AS ScoreTask, sp.ScoreExam AS ScoreExam,
                sp.ScoreFinal AS ScoreFinal, sp.ScoreGrade AS ScoreGrade, 
                sp.ScoreGradeValue AS ScoreGradeValue, 
                sp.ScorePoints AS ScorePoints, pr.Name AS Particiant, pr.ParticipantCode,
                c.Name AS Course , c.NameEng AS CourseEng, c.Credit,
                t.ID AS IDtimetable, t.PeriodeID, t.CourseID, t.PercentageTask, t.PercentageExam,
                p.ShowScoreTask, p.ShowScoreExam
                FROM db_training.study_plan sp 
                LEFT JOIN db_training.participant pr ON (pr.ParticipantCode = sp.ParticipantCode)
                LEFT JOIN db_training.timetable t ON (t.ID = sp.TimetableID)
                LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
                LEFT JOIN db_training.periode p ON (p.ID = sp.PeriodeID)
                WHERE sp.PeriodeID = "'.$d['ID'].'" AND sp.TimetableID = "'.$TimetableID.'" ')->result_array();
                if(count($datatimetable)>0){
                    for ($t=0; $t < count($datatimetable); $t++) { 
                        $dt = $datatimetable[$t];
                        // trainer
                        $datatimetable[$t]['dataTrainer'] = $this->db->query('SELECT t.IsCoordinator, 
                        TRIM(CONCAT(
                            COALESCE(tr.TitleAhead, NULL, "", "-" || tr.TitleAhead)
                            , " "
                            , tr.Name
                            , " "
                            , COALESCE(tr.TitleBehind, NULL, "", "-" || tr.TitleBehind))) AS Name, tr.TrainerCode
                        FROM db_training.timetable_trainer t 
                        LEFT JOIN db_training.trainer tr ON (tr.TrainerCode = t.TrainerCode)
                        WHERE t.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                        
                        // schedule
                        $datatimetable[$t]['dataSchedule'] = $this->db->query('SELECT 
                        CASE
                        WHEN NULLIF(td.Day, "") THEN d.NameEng
                        ELSE "" END
                        AS Day,
                        td.Date,
                        CONCAT(
                            SUBSTRING(td.TimeStart,1,5),
                            " ",
                            SUBSTRING(td.TimeEnd,1,5)
                            ) AS Time, 
                            td.IsOnline,
                            td.RoomID,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.timetable_details td 
                        LEFT JOIN db_training.m_classroom c ON (c.ID = td.RoomID)
                        LEFT JOIN db_training.day d ON (d.ID = td.Day)
                        WHERE td.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();

                        // exam schedule
                        $datatimetable[$t]['dataExamSchedule'] = $this->db->query('SELECT es.Date, 
                        CONCAT(
                            SUBSTRING(es.ExamStart,1,5),
                            " ",
                            SUBSTRING(es.ExamEnd,1,5)
                            ) AS Time,
                            es.IsOnline,
                            c.RoomCode, c.Name AS RoomName 
                        FROM db_training.exam_schedule_details esd 
                        LEFT JOIN db_training.exam_schedule es ON (es.ID = esd.ExamScheduleID)
                        LEFT JOIN db_training.m_classroom c ON (c.ID = es.RoomID)
                        WHERE esd.TimetableID = "'.$dt['TimetableID'].'" ')->result_array();
                    }

                }

                $dataPeriode[$i]['dataTimetable'] = $datatimetable;

            }

        return $dataPeriode;
        }
    }

    public function getListGrade($PeriodeID, $finalScore)
    {
        $data = $this->db->query('SELECT * FROM db_training.grade g
                                    WHERE g.RangeStart<= "'.$finalScore.'"
                                    AND g.RangeEnd >= "'.$finalScore.'" AND g.PeriodeID = "'.$PeriodeID.'" LIMIT 1')->result_array();
        
        return $data;
    }

    public function updatePercentage($ID,$PercentageTask,$PercentageExam)
    {
        // code...
        $this->db->where('ID', $ID);
        $this->db->update('db_training.timetable',array(
            'PercentageTask' => $PercentageTask,
            'PercentageExam' => $PercentageExam
        ));

        return 1;
    }

    public function createAtdTrainer($user_id,$dataAtd)
    {        
        $check = array(
                'TrainerCode' => $dataAtd['TrainerCode'],
                'TimetableID' => $dataAtd['TimetableID'],
                'Session' => $dataAtd['Session'],
  
            );
        $dataCk = $this->db->get_where('db_training.attendance',$check)->result_array();
            if(count($dataCk)>0){
                
                $this->db->where($check);
                $this->db->delete('db_training.attendance');
            }
        $insAtd = array(
            'TimetableID' => $dataAtd['TimetableID'],
            'Session' => $dataAtd['Session'],
            'TrainerCode' => $dataAtd['TrainerCode'],
            'Date' => $dataAtd['Date'],
            'StartTime' => $dataAtd['StartTime'],
            'EndTime' => $dataAtd['EndTime'],
            'InsertType' => 'admin',
            'InsertAt' => date('Y-m-d H:i:s')
        );
        $this->db->insert('db_training.attendance',$insAtd);        
        return 1;
    }

    public function readquizListAnswer($TimetableID,$Session,$IsForExam)
    {
        $whareQuiz = ($IsForExam=='0') 
        ? ' AND q.Session = "'.$Session.'" '
        : ' AND q.IsForExam = "'.$IsForExam.'" ';

        $dataQuiz = $this->db->query('SELECT q.*, t.Group, t.CourseID,
        c.CourseCode, c.Name AS CourseName, c.NameEng AS CourseNameEng 
        FROM db_training.q_quiz q 
        LEFT JOIN db_training.timetable t ON (t.ID = q.TimetableID)
        LEFT JOIN db_training.m_course c ON (c.ID = t.CourseID)
        WHERE q.TimetableID = "'.$TimetableID.'" '.$whareQuiz)->result_array();

        if(count($dataQuiz)>0){
            $dataQuiz[0]['dataQuestion'] = $this->db->query('SELECT 
            qd.QuestionID,
            qd.Point,
            q.Question,
            q.Note AS Question_Note,
            qt.Label AS Question_Type,
            qt.Descriptions AS Question_Type_Description
            FROM db_training.q_quiz_details qd
            LEFT JOIN db_training.q_question q ON (q.ID = qd.QuestionID)
            LEFT JOIN db_training.q_question_type qt ON (qt.ID = q.QTID)
            WHERE qd.QuizID = "'.$dataQuiz[0]['ID'].'" ')->result_array();

            if(count($dataQuiz[0]['dataQuestion'])>0){
                for ($i=0; $i < count($dataQuiz[0]['dataQuestion']); $i++) { 
                    $d = $dataQuiz[0]['dataQuestion'][$i];
                    $dataQuiz[0]['dataQuestion'][$i]['dataOptions'] = $this->db
                    ->get_where('db_training.q_question_options',array(
                        'QuestionID' => $d['QuestionID']
                    ))->result_array();
                }
            }

            $dataQuiz[0]['dataParticipant'] = $this->db->query('SELECT sp.*, p.Name,
            qa.ID AS QuizAnsID, qa.Date, qa.TimeStart, qa.TimeEnd, qa.SubmitedAt, qa.WorkingDuration
            FROM db_training.study_plan sp 
            LEFT JOIN db_training.participant p ON (p.ParticipantCode = sp.ParticipantCode)
            LEFT JOIN db_training.q_quiz_ans qa ON (qa.ParticipantCode = sp.ParticipantCode AND qa.QuizID = "'.$dataQuiz[0]['ID'].'")
            WHERE sp.TimetableID = "'.$TimetableID.'" ')->result_array();

            // get point 
            if(count($dataQuiz[0]['dataParticipant'])>0){
                for ($a=0; $a < count($dataQuiz[0]['dataParticipant']); $a++) { 
                    
                    $dp = $dataQuiz[0]['dataParticipant'][$a];
                    $dataPoint = $this->db->get_where('db_training.q_quiz_ans_details',
                    array(
                        'QuizAnsID' => $dp['QuizAnsID']
                    ))->result_array(); 
                    $showPoint = true;
                    $totalPoint = 0;
                    if(count($dataPoint)>0){
                        for ($p=0; $p < count($dataPoint); $p++) { 
                            if($dataPoint[$p]['Point']!='' && $dataPoint[$p]['Point']!=null){
                                $totalPoint = $totalPoint + (float) $dataPoint[$p]['Point'];
                            } else {
                                $showPoint = false;
                            }
                        }
                    } else {
                        $showPoint = false;
                    }

                    $dataQuiz[0]['dataParticipant'][$a]['point_showPoint'] = $showPoint;
                    $dataQuiz[0]['dataParticipant'][$a]['point_totalPoint'] = $totalPoint;

                    $checkAnswer = ($dp['SubmitedAt']!='' && $dp['SubmitedAt']!=null) ? 1 : 0;
                    $dataQuiz[0]['dataParticipant'][$a]['dataQuestion'] = $this->showQuestionInQuiz($dp['ParticipantCode'],$dataQuiz[0]['ID'],$checkAnswer,0);

                }
                

                

                
            }
        }

        return $dataQuiz;
    }

    public function quizPointUpdate($QuizAnsDetailID,$Point)
    {
        $this->db->where('ID', $QuizAnsDetailID);
                        $this->db->update('db_training.q_quiz_ans_details',array(
                            'Point' => $Point
                        ));

        return 1;
    }

    public function checkAttendaceParticipant($user_id,$ForumID,$QuizID)
    {
        // get sesi
        if($ForumID!='' || $QuizID!='') {
            $ID = ($ForumID!='') ? $ForumID : $QuizID;
            $table = ($ForumID!='') ? 'forum' : 'q_quiz';

            $dataSession = $this->db
            ->select('a.TimetableID,a.Session, b.PeriodeID, c.AttdForum, c.AttdTask')
            ->join('db_training.timetable AS b','b.ID = a.TimetableID','left')
            ->join('db_training.periode AS c','c.ID = b.PeriodeID','left')
            ->where(array('a.ID' => $ID))
            ->get('db_training.'.$table.' AS a')->result_array();

            if(count($dataSession)>0){
                $Session = $dataSession[0]['Session'];
                $TimetableID = $dataSession[0]['TimetableID'];

                $AttdForum = $dataSession[0]['AttdForum'];
                $AttdTask = $dataSession[0]['AttdTask'];

                $dataWhereID = array('TimetableID' => $TimetableID,
                'Session' => $Session);

                // get quiz id
                if($ForumID!=''){
                    $dataQuizID = $this->db->get_where('db_training.q_quiz',$dataWhereID)->result_array();

                    $QuizID = (count($dataQuizID)>0) ? $dataQuizID[0]['ID'] : '';
                }
                // get forum id
                else {
                    $dataForumID = $this->db->get_where('db_training.forum',$dataWhereID)->result_array();
                    $ForumID = (count($dataForumID)>0) ? $dataForumID[0]['ID'] : '';
                }

                $StatusAttdForum = ($AttdForum=='1') ? false : true;
                $StatusAttdTask = ($AttdTask=='1') ? false : true;

                if($AttdForum=='1' && $ForumID!=''){
                    $Count_Forum = $this->db->from('db_training.forum_details')
                                ->where(array(
                                    'ForumID' => $ForumID,
                                    'ParticipantCode' => $user_id
                                ))->count_all_results();

                    $StatusAttdForum = ($Count_Forum>0) ? true : false;
                }

                if($AttdTask=='1' && $QuizID!=''){
                    $Count_Quiz = $this->db->from('db_training.q_quiz_ans')
                                ->where(array(
                                    'QuizID' => $QuizID,
                                    'ParticipantCode' => $user_id,
                                ))
                                ->where('SubmitedAt is NOT NULL', NULL, FALSE)
                                ->count_all_results();
                    $StatusAttdTask = ($Count_Quiz>0) ? true : false;
                }

                if($StatusAttdForum==true && $StatusAttdTask==true){

                    $dataCkattd = $this->db->get_where('db_training.attendance',array(
                        'TimetableID' => $TimetableID,
                        'ParticipantCode' => $user_id,
                        'Session' => $Session
                    ))->result_array();

                    if(count($dataCkattd)<=0){
                        $insAtd = array(
                            'TimetableID' => $TimetableID,
                            'ParticipantCode' => $user_id,
                            'Session' => $Session,
                            'Status' => '1',
                            'InsertType' => 'auto',
                            'InsertAt' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('db_training.attendance',$insAtd);
                    }

                }
                
            }
        }

        return 1;
    }

    public function checkAttendaceTrainer($user_id,$ForumID)
    {

        $dataCKForum = $this->db
            ->select('b.TimetableID, b.Session')
            ->join('db_training.forum AS b','b.ID = a.ForumID','left')
            ->where(array(
            'a.ForumID' => $ForumID,
            'a.TrainerCode' => $user_id))
            ->limit(1)
            ->get('db_training.forum_details AS a')
            ->result_array();        

        if(count($dataCKForum)>0){
            $Session = $dataCKForum[0]['Session'];
            $TimetableID = $dataCKForum[0]['TimetableID'];

            $dataCkattd = $this->db->get_where('db_training.attendance',array(
                    'TimetableID' => $TimetableID,
                    'TrainerCode' => $user_id,
                    'Session' => $Session
                ))->result_array();

            if(count($dataCkattd)<=0){
                $arrIns = array(
                    'TimetableID' => $TimetableID,
                    'TrainerCode' => $user_id,
                    'Date' => date('Y-m-d'),
                    'StartTime' => date('H:i:s'),
                    'EndTime' => date('H:i:s'),
                    'InsertType' => 'auto'
                );

                $this->db->insert('db_training.attendance',$arrIns);
            }

            // cek apakah sudah ngisi forum atau blm
            

        }
    }


}
