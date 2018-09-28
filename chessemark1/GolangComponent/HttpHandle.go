package main

import (
	"log"
	"io/ioutil"
	"net/http"
	"encoding/json"
	"strconv"
)

type Error struct {
	Code int
	Data string
}

func InitHeader(w http.ResponseWriter) {
	w.Header().Set("Access-Control-Allow-Origin", "*")             //允许访问所有域
	w.Header().Add("Access-Control-Allow-Headers", "Content-Type") //header的类型
}

func DisplayByHtml(w http.ResponseWriter, htmlFile string) {
	htmlText, _ := ioutil.ReadFile(htmlFile)
	w.Write(htmlText)
}

func DisplayByJson(w http.ResponseWriter, data []byte) {
	w.Header().Set("content-type", "application/json") //返回数据格式是json

	w.Write(data)
}

func Auth(w http.ResponseWriter, req *http.Request) bool {
	token := req.URL.Query().Get("token")

	str, _ := ioutil.ReadFile("token")
	if string(str) != token {
		data := Error{}
		data.Code = 102
		data.Data = "NOT_AUTH"
		dataText, _ := json.Marshal(data)
		DisplayByJson(w, dataText)
		return false
	}
	return true
}

func SetTask(w http.ResponseWriter, req *http.Request) {
	InitHeader(w)
	is := Auth(w, req)
	if !is {
		return
	}
	defer req.Body.Close()

	tr := TaskRun
	TaskRun = false

	body, err := ioutil.ReadAll(req.Body)
	if err != nil {
		log.Print(err)
	}
	ioutil.WriteFile(ConfFile, body, 0644)
	taskDataJsonText, _ := ioutil.ReadFile(ConfFile)

	TaskRun = tr
	DisplayByJson(w, taskDataJsonText)
}

func Task(w http.ResponseWriter, req *http.Request) {
	InitHeader(w)
	is := Auth(w, req)
	if !is {
		return
	}

	data := make(map[string]string)

	taskDataJsonText, _ := ioutil.ReadFile(ConfFile)
	data["Task"] = string(taskDataJsonText)
	data["IsRun"] = strconv.FormatBool(TaskRun)

	dataText, _ := json.Marshal(data)
	DisplayByJson(w, dataText)
}

func Stop(w http.ResponseWriter, req *http.Request) {
	InitHeader(w)
	is := Auth(w, req)
	if !is {
		return
	}
	TaskRun = false

	data := make(map[string]string)

	taskDataJsonText, _ := ioutil.ReadFile(ConfFile)
	data["Task"] = string(taskDataJsonText)
	data["IsRun"] = strconv.FormatBool(TaskRun)

	dataText, _ := json.Marshal(data)
	DisplayByJson(w, dataText)
}

func Start(w http.ResponseWriter, req *http.Request) {
	InitHeader(w)
	is := Auth(w, req)
	if !is {
		return
	}
	TaskRun = true

	data := make(map[string]string)

	taskDataJsonText, _ := ioutil.ReadFile(ConfFile)
	data["Task"] = string(taskDataJsonText)
	data["IsRun"] = strconv.FormatBool(TaskRun)

	dataText, _ := json.Marshal(data)
	DisplayByJson(w, dataText)
}

func Index(w http.ResponseWriter, req *http.Request) {
	InitHeader(w)

	//log.Print("Index")
	w.Write([]byte("hello"))
}
