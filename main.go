package main

import (
	"log"
	"net/http"
	"os"

	"main/handlers"
	"main/middleware"
	"main/models"
	"github.com/gorilla/mux"
	"github.com/joho/godotenv"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

var db *gorm.DB

func initDB() {
	var err error
	dsn := os.Getenv("DATABASE_URL")
	if dsn == "" {
		log.Fatal("DATABASE_URL environment variable is not set")
	}
	
	db, err = gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		log.Fatal("Failed to initialize database, got error ", err)
	}

	// Auto Migrate the schemas
	err = db.AutoMigrate(&models.User{}, &models.Service{}, &models.Booking{}, &models.Category{})
	if err != nil {
		log.Fatal("Failed to migrate database schemas:", err)
	}
}

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Println("Error loading .env file")
	}

	initDB()
	handlers.InitDB(db)

	r := mux.NewRouter()

	// Auth routes
	r.HandleFunc("/api/register", handlers.RegisterHandler).Methods("POST")
	r.HandleFunc("/api/login", handlers.LoginHandler).Methods("POST")

	// Protected routes
	api := r.PathPrefix("/api").Subrouter()
	api.Use(middleware.AuthMiddleware)

	// Service routes
	api.HandleFunc("/services", handlers.GetServicesHandler).Methods("GET")
	api.HandleFunc("/services", handlers.CreateServiceHandler).Methods("POST")
	api.HandleFunc("/services/{id}", handlers.UpdateServiceHandler).Methods("PUT")
	api.HandleFunc("/services/{id}", handlers.DeleteServiceHandler).Methods("DELETE")

	// Booking routes  
	api.HandleFunc("/bookings", handlers.GetBookingsHandler).Methods("GET")
	api.HandleFunc("/bookings", handlers.CreateBookingHandler).Methods("POST")
	api.HandleFunc("/bookings/{id}/status", handlers.UpdateBookingStatusHandler).Methods("PUT")

	// Worker routes
	api.HandleFunc("/workers", handlers.GetWorkersHandler).Methods("GET")
	api.HandleFunc("/workers/status", handlers.UpdateWorkerStatusHandler).Methods("POST")

	// Category routes
	api.HandleFunc("/categories", handlers.GetCategoriesHandler).Methods("GET")

	log.Println("Server starting on port 5000...")
	log.Fatal(http.ListenAndServe("0.0.0.0:5000", r))
}