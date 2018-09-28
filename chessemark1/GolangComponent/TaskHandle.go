package main

import (
	"io/ioutil"
	"encoding/json"
	"time"
	"net/http"
)

var TaskRun = true

func TaskHandle() {
	var taskData []taskConfig
	go func() {
		for ; ; {
			if TaskRun {
				taskDataJsonText, _ := ioutil.ReadFile(ConfFile)
				json.Unmarshal(taskDataJsonText, &taskData)
				for i := 0; i < len(taskData); i++ {
					t := time.Now().Unix()
					if taskData[i].LastRunTime <= t {
						taskData[i].LastRunTime = t + taskData[i].IntervalSec
						http.Get(taskData[i].Url)
						//log.Print(taskData[i].Name)
					}
				}
				data, err := json.Marshal(taskData)
				if err == nil {
					ioutil.WriteFile(ConfFile, data, 0644)
				}
			}
			time.Sleep(time.Second / 10)
		}
	}()
}
