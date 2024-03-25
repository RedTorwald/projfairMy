curl -X POST -H "Content-Type: application/json" -d '{
  "created_at": "2023-08-11 13:35:08",
  "update_at": "2023-05-22 14:06:57",
  "priority": "1",
  "review": "NULL",
  "project_id": "550",
  "candidate_id": "668",
  "state_iod": "4"
}' https://projfair.istu.edu/project/550/candidates


curl -X POST -H "Content-Type: application/json" -d '{"created_at": 2023-08-11 13:35:08", "update_at": "2023-05-22 14:06:57", "priority": 1, "review": null,  "project_id": 550, "candidate_id": 668, "state_id": 4}' http://127.0.0.1:8000/project/api/participations/${550}