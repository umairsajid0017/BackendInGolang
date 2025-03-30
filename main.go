
package main

import (
	"log"
	"net/http"
	"os"

	"github.com/gorilla/mux"
	"github.com/joho/godotenv"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

var db *gorm.DB

func initDB() {
	var err error
	dsn := os.Getenv("DATABASE_URL")
	db, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}
}

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Println("Error loading .env file")
	}

	initDB()

	r := mux.NewRouter()
	
	// Auth routes
	r.HandleFunc("/api/register", RegisterHandler).Methods("POST")
	r.HandleFunc("/api/login", LoginHandler).Methods("POST")

	// Protected routes
	api := r.PathPrefix("/api").Subrouter()
	api.Use(AuthMiddleware)

	// Service routes
	api.HandleFunc("/services", GetServicesHandler).Methods("GET")
	api.HandleFunc("/services", CreateServiceHandler).Methods("POST")
	api.HandleFunc("/services/{id}", UpdateServiceHandler).Methods("PUT")
	api.HandleFunc("/services/{id}", DeleteServiceHandler).Methods("DELETE")

	// Booking routes  
	api.HandleFunc("/bookings", GetBookingsHandler).Methods("GET")
	api.HandleFunc("/bookings", CreateBookingHandler).Methods("POST")
	api.HandleFunc("/bookings/{id}/status", UpdateBookingStatusHandler).Methods("PUT")

	// Worker routes
	api.HandleFunc("/workers", GetWorkersHandler).Methods("GET")
	api.HandleFunc("/workers/status", UpdateWorkerStatusHandler).Methods("POST")

	// Category routes
	api.HandleFunc("/categories", GetCategoriesHandler).Methods("GET")

	log.Println("Server starting on port 5000...")
	log.Fatal(http.ListenAndServe("0.0.0.0:5000", r))
}
