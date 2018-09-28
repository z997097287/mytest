package main

import (
	"net/http"
	"log"
	"io/ioutil"
	"os"
	"./uuid"
	"time"
)

const ConfFile = "TaskData.json"

func Init() {
	_, err := os.Stat(ConfFile)
	if err != nil {
		if os.IsNotExist(err) {
			log.Print("Init config:TaskData.json")

			err := ioutil.WriteFile(ConfFile, []byte("[]"), 0644)
			if err != nil {
				log.Print(err)
			}
		}
	}
	_, err2 := os.Stat("token")
	if err2 != nil {
		if os.IsNotExist(err2) {
			token := uuid.Must(uuid.NewV4())
			log.Print("token:" + token.String())
			ioutil.WriteFile("token", []byte(token.String()), 0644)
		}
	}
}
func main() {
	TaskHandle()
	Init()
	http.DefaultClient.Timeout = time.Second * 10
	http.HandleFunc("/", Index)          //设置访问的路由
	http.HandleFunc("/Task", Task)       //设置访问的路由
	http.HandleFunc("/SetTask", SetTask) //设置访问的路由
	http.HandleFunc("/Stop", Stop)       //设置访问的路由
	http.HandleFunc("/Start", Start)     //设置访问的路由
	http.Handle("/html/", http.StripPrefix("/html/", http.FileServer(http.Dir("./html/"))))

	err := http.ListenAndServe(":39090", nil) //设置监听的端口
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}

}
