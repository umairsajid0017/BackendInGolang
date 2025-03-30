
package models

import (
	"time"

	"gorm.io/gorm"
)

type User struct {
	ID              uint64     `gorm:"primaryKey;autoIncrement" json:"id"`
	LoginType       string     `json:"login_type"`
	WorkerType      string     `json:"worker_type"`
	IsOnline        int        `json:"is_online"`
	OtpVerify       int        `json:"otp_verify"`
	Name            string     `json:"name"`
	Email           string     `json:"email"`
	Phone           string     `json:"phone"`
	EmailVerifiedAt *time.Time `json:"email_verified_at"`
	Password        string     `json:"-"`
	Image           string     `json:"image"`
	Role           string     `json:"role"`
	CreatedAt      time.Time  `json:"created_at"`
	UpdatedAt      time.Time  `json:"updated_at"`
	DeletedAt      *time.Time `json:"deleted_at"`
}

type Service struct {
	ID          uint64     `gorm:"primaryKey;autoIncrement" json:"id"`
	Name        string     `json:"name"`
	Images      string     `json:"images"`
	Description string     `json:"description"`
	FixedPrice  string     `json:"fixed_price"`
	HourlyPrice string     `json:"hourly_price"`
	Address     string     `json:"address"`
	CategoryID  uint64     `json:"category_id"`
	UserID      uint64     `json:"user_id"`
	AddedBy     string     `json:"added_by"`
	CreatedAt   time.Time  `json:"created_at"`
	UpdatedAt   time.Time  `json:"updated_at"`
	DeletedAt   *time.Time `json:"deleted_at"`
}

type Booking struct {
	ID              uint64     `gorm:"primaryKey;autoIncrement" json:"id"`
	BookingNumber   string     `json:"booking_number"`
	BookingDate     time.Time  `json:"booking_date"`
	CustomerID      uint64     `json:"customer_id"`
	WorkerID        uint64     `json:"worker_id"`
	ServiceID       uint64     `json:"service_id"`
	Status          int        `json:"status"`
	Address         string     `json:"address"`
	Price           string     `json:"price"`
	CreatedAt       time.Time  `json:"created_at"`
	UpdatedAt       time.Time  `json:"updated_at"`
	DeletedAt       *time.Time `json:"deleted_at"`
}

type Category struct {
	ID        uint64     `gorm:"primaryKey;autoIncrement" json:"id"`
	Title     string     `json:"title"`
	Image     string     `json:"image"`
	Status    int        `json:"status"`
	CreatedAt time.Time  `json:"created_at"`
	UpdatedAt time.Time  `json:"updated_at"`
	DeletedAt *time.Time `json:"deleted_at"`
}
