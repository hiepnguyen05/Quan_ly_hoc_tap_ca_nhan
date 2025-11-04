# 🎨 Design System - StudyHub

## Tài Liệu Màu Sắc và Bố Cục Trang Chủ

---

## 📊 Mục Lục
1. [Bảng Màu Chính (Color Palette)](#bảng-màu-chính)
2. [Màu Sắc Theo Thành Phần](#màu-sắc-theo-thành-phần)
3. [Bố Cục Trang Chủ](#bố-cục-trang-chủ)
4. [Responsive Design](#responsive-design)
5. [Typography & Spacing](#typography--spacing)

---

## 🎨 Bảng Màu Chính

### Primary Colors (Màu Chủ Đạo)

```css
/* Brand Colors */
--primary: #030213           /* Đen đậm - Logo, Text */
--blue-gradient-start: #3B82F6  /* Xanh dương - Gradient chính */
--blue-gradient-end: #8B5CF6    /* Tím - Gradient phụ */
```

**Sử dụng:**
- Logo gradient
- Buttons chính
- Hover states
- Focus rings

---

### Semantic Colors (Màu Ngữ Nghĩa)

```css
/* Màu cho các trạng thái cụ thể */

🔵 Blue (Xanh Dương) - Thông tin, Học tập
--blue-50:  #EFF6FF    /* Background nhạt */
--blue-100: #DBEAFE    /* Background card */
--blue-600: #3B82F6    /* Icon, text */

🟢 Green (Xanh Lá) - Thành công, Tiến độ
--green-50:  #F0FDF4   /* Background nhạt */
--green-100: #DCFCE7   /* Background card */
--green-600: #10B981   /* Icon, text */
--green-700: #059669   /* Text đậm */

🟠 Orange (Cam) - Cảnh báo, Thời gian
--orange-50:  #FFF7ED  /* Background nhạt */
--orange-100: #FFEDD5  /* Background card */
--orange-600: #F59E0B  /* Icon, text */

🟣 Purple (Tím) - Đặc biệt, Premium
--purple-50:  #FAF5FF  /* Background nhạt */
--purple-100: #F3E8FF  /* Background card */
--purple-600: #8B5CF6  /* Icon, text */

🔴 Red (Đỏ) - Lỗi, Quan trọng, Quá hạn
--red-50:  #FEF2F2    /* Background nhạt */
--red-100: #FEE2E2    /* Background card */
--red-600: #EF4444    /* Icon, text, Error */
--red-700: #DC2626    /* Text đậm */

🟡 Yellow (Vàng) - Chú ý, Pending
--yellow-100: #FEF3C7  /* Background card */
--yellow-700: #A16207  /* Text */
```

---

### Neutral Colors (Màu Trung Tính)

```css
/* Grayscale - Dùng cho UI elements */

--white:     #FFFFFF    /* Background chính */
--gray-50:   #F9FAFB    /* Background page */
--gray-100:  #F3F4F6    /* Background secondary */
--gray-200:  #E5E7EB    /* Borders nhẹ */
--gray-300:  #D1D5DB    /* Borders */
--gray-400:  #9CA3AF    /* Icons disabled */
--gray-500:  #6B7280    /* Text phụ */
--gray-600:  #4B5563    /* Text secondary */
--gray-700:  #374151    /* Text primary */
--gray-800:  #1F2937    /* Text đậm, Sidebar dark */
--gray-900:  #111827    /* Heading, Text emphasis */
```

---

## 🧩 Màu Sắc Theo Thành Phần

### 1. Header (Navigation Bar)

```css
Background: #FFFFFF (white)
Border: rgba(0, 0, 0, 0.1) (1px bottom)
Height: 73px
Position: Sticky top

Logo Container:
  - Background: linear-gradient(to-br, #3B82F6, #8B5CF6)
  - Size: 40px × 40px
  - Border-radius: 8px
  - Icon color: #FFFFFF

Menu Items:
  - Text color: #374151 (gray-700)
  - Hover: #F3F4F6 (gray-100) background
  - Active: #EFF6FF (blue-50) background + #3B82F6 text

User Avatar:
  - Background: #3B82F6 (blue-600)
  - Text: #FFFFFF
  - Size: 40px × 40px
  - Border-radius: 50%
```

---

### 2. Sidebar (Navigation)

```css
Background: #FFFFFF
Border-right: 1px solid #E5E7EB (gray-200)
Width: 256px (64 tailwind units)
Height: calc(100vh - 73px)

Menu Item (Inactive):
  - Background: transparent
  - Text: #374151 (gray-700)
  - Icon: #374151
  - Hover background: #F3F4F6 (gray-100)

Menu Item (Active):
  - Background: #EFF6FF (blue-50)
  - Text: #3B82F6 (blue-600)
  - Icon: #3B82F6
  - Border-left: 3px solid #3B82F6 (optional)
```

---

### 3. Stats Cards (Dashboard)

**Card Container:**
```css
Background: #FFFFFF
Border: 1px solid #E5E7EB
Border-radius: 12px (0.75rem)
Padding: 24px (1.5rem)
Shadow: 0 1px 3px rgba(0,0,0,0.1)
```

**Stat 1 - Môn Học (Blue):**
```css
Icon container:
  - Background: #DBEAFE (blue-100)
  - Icon color: #3B82F6 (blue-600)
  - Size: 48px × 48px
  - Border-radius: 8px
  
Label: #4B5563 (gray-600)
Value: #111827 (gray-900) - text-2xl
```

**Stat 2 - Bài Tập (Green):**
```css
Icon container:
  - Background: #DCFCE7 (green-100)
  - Icon color: #10B981 (green-600)
```

**Stat 3 - Giờ Học (Orange):**
```css
Icon container:
  - Background: #FFEDD5 (orange-100)
  - Icon color: #F59E0B (orange-600)
```

**Stat 4 - Tiến Độ (Purple):**
```css
Icon container:
  - Background: #F3E8FF (purple-100)
  - Icon color: #8B5CF6 (purple-600)
```

---

### 4. Subject Cards (Môn Học)

```css
Card Background: #FFFFFF
Border: 1px solid #E5E7EB
Border-radius: 12px
Padding: 24px
Hover: Shadow increase (0 4px 6px rgba(0,0,0,0.1))

Icon Container (Dynamic):
  - Background: {subject.color}20 (màu môn học với opacity 20%)
  - Icon color: {subject.color} (màu thực của môn)
  - Size: 48px × 48px
  - Border-radius: 8px

Subject Colors:
  - Toán Cao Cấp: #3B82F6 (blue-600)
  - Lập Trình Web: #10B981 (green-600)
  - Cơ Sở Dữ Liệu: #F59E0B (orange-600)
  - Tiếng Anh: #EF4444 (red-600)

Progress Bar:
  - Background: #E5E7EB (gray-200)
  - Fill: {subject.color}
  - Height: 8px
  - Border-radius: 4px
```

---

### 5. Assignment List (Bài Tập)

```css
Container Card:
  - Background: #FFFFFF
  - Padding: 24px
  - Border-radius: 12px

Assignment Item:
  - Background: #FFFFFF
  - Border: 1px solid #E5E7EB
  - Border-radius: 8px
  - Padding: 16px
  - Hover: #F9FAFB (gray-50)

Status Badges:
  Pending (Chưa làm):
    - Background: #F3F4F6 (gray-100)
    - Text: #374151 (gray-700)
  
  In-Progress (Đang làm):
    - Background: #DBEAFE (blue-100)
    - Text: #1E40AF (blue-700)
  
  Completed (Hoàn thành):
    - Background: #DCFCE7 (green-100)
    - Text: #059669 (green-700)

Priority Badges:
  High (Cao):
    - Background: #FEE2E2 (red-100)
    - Text: #DC2626 (red-700)
  
  Medium (Trung bình):
    - Background: #FEF3C7 (yellow-100)
    - Text: #A16207 (yellow-700)
  
  Low (Thấp):
    - Background: #DCFCE7 (green-100)
    - Text: #059669 (green-700)

Overdue Warning:
  - Text color: #DC2626 (red-600)
  - Icon: #DC2626
```

---

### 6. Charts (Biểu Đồ)

```css
Container:
  - Background: #FFFFFF
  - Padding: 24px
  - Border-radius: 12px

Bar Chart (Thời gian học):
  - Bars color: #3B82F6 (blue-600)
  - Grid: #E5E7EB (gray-200)
  - Text: #6B7280 (gray-500)
  - Border-radius bars: 8px 8px 0 0

Radar Chart (Hiệu suất):
  - Line stroke: #10B981 (green-600)
  - Fill: #10B981 with 60% opacity
  - Grid: #E5E7EB
```

---

### 7. Login/Register Page

```css
Page Background:
  - Gradient: linear-gradient(to-br, #EFF6FF, #FFFFFF, #FAF5FF)
  - (from-blue-50 via-white to-purple-50)

Logo Container:
  - Background: linear-gradient(to-br, #3B82F6, #8B5CF6)
  - Size: 64px × 64px
  - Border-radius: 16px
  - Icon: #FFFFFF

Card:
  - Background: #FFFFFF
  - Padding: 24px
  - Border-radius: 12px
  - Shadow: 0 10px 25px rgba(0,0,0,0.1)

Form Elements:
  Input:
    - Background: #F3F3F5 (input-background)
    - Border: transparent → focus: #3B82F6
    - Icon color: #9CA3AF (gray-400)
    - Text: #111827
  
  Button Primary:
    - Background: #030213 (primary/black)
    - Text: #FFFFFF
    - Hover: opacity 90%
  
  Links:
    - Color: #3B82F6 (blue-600)
    - Hover: underline

Demo Info Box:
  - Background: #EFF6FF (blue-50)
  - Border-radius: 8px
  - Text: #1E40AF (blue-800) heading
  - Text: #3B82F6 (blue-600) credentials
```

---

### 8. Study Plan Cards

```css
Status Colors:

Active (Đang thực hiện):
  - Badge bg: #DBEAFE (blue-100)
  - Badge text: #1E40AF (blue-700)

Completed (Hoàn thành):
  - Badge bg: #DCFCE7 (green-100)
  - Badge text: #059669 (green-700)

Upcoming (Sắp tới):
  - Badge bg: #F3F4F6 (gray-100)
  - Badge text: #374151 (gray-700)

Icons:
  - Calendar: #3B82F6 (blue-600)
  - Target: #10B981 (green-600)
  - Clock: #6B7280 (gray-500)
```

---

### 9. Admin Dashboard

```css
Stats Cards (Admin):
  Blue (Students):
    - Background: #DBEAFE (blue-100)
    - Icon: #3B82F6 (blue-600)
  
  Green (Subjects):
    - Background: #DCFCE7 (green-100)
    - Icon: #10B981 (green-600)
  
  Orange (Assignments):
    - Background: #FFEDD5 (orange-100)
    - Icon: #F59E0B (orange-600)
  
  Purple (Active Users):
    - Background: #F3E8FF (purple-100)
    - Icon: #8B5CF6 (purple-600)

Table:
  - Header bg: #F9FAFB (gray-50)
  - Header text: #374151 (gray-700)
  - Row hover: #F9FAFB (gray-50)
  - Border: #E5E7EB (gray-200)
  
  Status Active:
    - Badge bg: #DCFCE7 (green-100)
    - Badge text: #059669 (green-700)
  
  Status Inactive:
    - Badge bg: #F3F4F6 (gray-100)
    - Badge text: #6B7280 (gray-500)
```

---

## 📐 Bố Cục Trang Chủ

### Layout Structure (Cấu trúc tổng thể)

```
┌─────────────────────────────────────────────────────────────┐
│  HEADER (Sticky)                                 73px       │
│  ┌──────┐  StudyHub        [Search] [Settings] [@Avatar]   │
│  │ Logo │                                                   │
│  └──────┘                                                   │
├─────────────┬───────────────────────────────────────────────┤
│             │                                               │
│  SIDEBAR    │  MAIN CONTENT                                │
│  256px      │                                               │
│             │                                               │
│  Dashboard  │  ┌─────────────────────────────────────────┐ │
│  Kế Hoạch   │  │  Page Title & Actions                   │ │
│  Môn Học    │  └─────────────────────────────────────────┘ │
│  Bài Tập    │                                               │
│  Ghi Chú    │  ┌─────┬─────┬─────┬─────┐                  │
│  Phân Tích  │  │Stat │Stat │Stat │Stat │  Stats Row       │
│             │  │ 1   │ 2   │ 3   │ 4   │                  │
│             │  └─────┴─────┴─────┴─────┘                  │
│             │                                               │
│             │  ┌──────────────────┬────────────┐           │
│             │  │                  │            │           │
│             │  │  Main Content    │  Sidebar   │  2 Cols  │
│             │  │  (2/3 width)     │  (1/3)     │           │
│             │  │                  │            │           │
│             │  │  - Assignments   │  - Calendar│           │
│             │  │  - Chart         │  - Notes   │           │
│             │  │                  │            │           │
│             │  └──────────────────┴────────────┘           │
│             │                                               │
└─────────────┴───────────────────────────────────────────────┘
```

---

### Dashboard Page Layout (Chi tiết)

```
┌─────────────────────────────────────────────────────────────┐
│  Xin chào! 👋                                               │
│  Hôm nay là Thứ 5, ngày 30 tháng 10 năm 2025              │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────┬──────────┬──────────┬──────────┐            │
│  │ 📚 Tổng  │ 📋 Bài   │ ⏰ Giờ   │ 📈 Tiến  │  4 Stats  │
│  │ Môn Học  │ Tập      │ Học      │ Độ       │            │
│  │    4     │    3     │   27h    │  67.5%   │            │
│  └──────────┴──────────┴──────────┴──────────┘            │
├────────────────────────────────────┬────────────────────────┤
│                                    │                        │
│  ┌──────────────────────────────┐ │  ┌──────────────────┐ │
│  │  Bài Tập Sắp Tới            │ │  │  Lịch Học        │ │
│  │  ────────────────────────    │ │  │  Calendar Widget │ │
│  │  □ Bài tập Giải tích 1      │ │  │                  │ │
│  │    Toán Cao Cấp              │ │  │  [Calendar]      │ │
│  │    📅 05/11/2025  🔴 Cao     │ │  │                  │ │
│  │                              │ │  │  Hôm nay:        │ │
│  │  □ Dự án Website cá nhân    │ │  │  • Toán 7-9h     │ │
│  │    Lập Trình Web             │ │  │  • Web 13-15h    │ │
│  │    📅 10/11/2025  🔴 Cao     │ │  └──────────────────┘ │
│  │                              │ │                        │
│  │  ✓ Thiết kế ERD              │ │  ┌──────────────────┐ │
│  │    CSDL                      │ │  │  Ghi Chú Gần Đây │ │
│  │    📅 03/11/2025  🟡 TB      │ │  │  ────────────    │ │
│  └──────────────────────────────┘ │  │  📝 Công thức   │ │
│                                    │  │     đạo hàm     │ │
│  ┌──────────────────────────────┐ │  │                  │ │
│  │  Thời Gian Học Trong Tuần   │ │  │  📝 React Hooks │ │
│  │  ────────────────────────    │ │  │                  │ │
│  │                              │ │  │  📝 Chuẩn hóa   │ │
│  │      📊 Bar Chart            │ │  │     CSDL         │ │
│  │      (Giờ/Ngày)              │ │  └──────────────────┘ │
│  │                              │ │                        │
│  └──────────────────────────────┘ │                        │
│                                    │                        │
└────────────────────────────────────┴────────────────────────┘

Grid System:
- Container: max-width 1140px, centered
- Stats Row: 4 columns (1 col on mobile)
- Main Area: 2 columns (lg:col-span-2 vs lg:col-span-1)
- Gap: 24px (1.5rem)
```

---

### Subjects Page Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Môn Học Của Tôi                        [+ Thêm Môn Học]   │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────┬──────────────┬──────────────┐            │
│  │  📘          │  📗          │  📙          │            │
│  │  Toán Cao    │  Lập Trình   │  Cơ Sở Dữ   │  3 cols   │
│  │  Cấp         │  Web         │  Liệu        │            │
│  │  4 tín chỉ   │  3 tín chỉ   │  3 tín chỉ   │            │
│  │              │              │              │            │
│  │  GV: TS. ...│  GV: ThS. ...│  GV: TS. ... │            │
│  │  Lịch: T2,4  │  Lịch: T3,5  │  Lịch: T4,6  │            │
│  │              │              │              │            │
│  │  ████░░ 75% │  ███░░░ 60%  │  ████░ 85%   │  Progress │
│  └──────────────┴──────────────┴──────────────┘            │
│                                                             │
│  ┌──────────────┐                                          │
│  │  📕          │                                          │
│  │  Tiếng Anh   │                                          │
│  │  Chuyên Ngành│                                          │
│  └──────────────┘                                          │
└─────────────────────────────────────────────────────────────┘

Grid: 3 columns desktop, 2 tablet, 1 mobile
```

---

### Study Plan Page Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Kế Hoạch Học Tập                    [+ Tạo Kế Hoạch Mới]  │
│  Quản lý và theo dõi kế hoạch học tập của bạn              │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────┬──────────────┬──────────────┐            │
│  │ 📅 Đang      │ 📈 Hoàn      │ 📅 Sắp Tới  │  3 Stats  │
│  │ Thực Hiện    │ Thành        │              │            │
│  │      1       │      1       │      1       │            │
│  └──────────────┴──────────────┴──────────────┘            │
├─────────────────────────────────────────────────────────────┤
│  [Tất Cả] [Đang Thực Hiện] [Sắp Tới] [Hoàn Thành]  Tabs   │
├─────────────────────────────────────────────────────────────┤
│  ┌────────────────┬────────────────┬────────────────┐      │
│  │ Kế Hoạch Ôn   │ Kế Hoạch Tuần │ Chuẩn Bị Thi  │      │
│  │ Thi Giữa Kỳ   │ Cuối Tháng 10 │ Cuối Kỳ       │      │
│  │ 🔵 Đang làm   │ 🟢 Hoàn thành │ ⚪ Sắp tới    │      │
│  │               │               │               │      │
│  │ 📅 01-15/11   │ 📅 28-31/10   │ 📅 01-25/12   │      │
│  │ 🎯 4 mục tiêu │ 🎯 3 mục tiêu │ 🎯 3 mục tiêu │      │
│  │ ✓ 2/7 buổi    │ ✓ 3/3 buổi    │ ✓ 0/0 buổi    │      │
│  │               │               │               │      │
│  │ ████░░░░ 28%  │ ████████ 100% │ ░░░░░░░░ 0%   │      │
│  │               │               │               │      │
│  │ [Xem Chi Tiết]│ [Xem Chi Tiết]│ [Xem Chi Tiết]│      │
│  └────────────────┴────────────────┴────────────────┘      │
└─────────────────────────────────────────────────────────────┘

Grid: 3 columns desktop, 2 tablet, 1 mobile
```

---

### Admin Dashboard Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Quản Trị Hệ Thống                                          │
│  Tổng quan và quản lý toàn bộ hệ thống                      │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────┬──────────┬──────────┬──────────┐  4 Stats   │
│  │ 👥       │ 📚       │ 📋       │ 📊       │            │
│  │ Tổng SV  │ Tổng MH  │ BT Chờ   │ User     │            │
│  │   156    │    24    │    89    │ Active   │            │
│  │ +12%     │ +3 môn   │ +8%      │   124    │            │
│  └──────────┴──────────┴──────────┴──────────┘            │
├─────────────────────────────────────────────────────────────┤
│  [Sinh Viên] [Môn Học] [Bài Tập]           Tabs            │
├─────────────────────────────────────────────────────────────┤
│  [🔍 Tìm kiếm sinh viên...]      [+ Thêm Sinh Viên]       │
├─────────────────────────────────────────────────────────────┤
│  TABLE:                                                     │
│  ┌────────────┬─────────────────┬────┬────┬────────┬───┐  │
│  │ Họ Tên     │ Email           │ MH │ BT │ Status │...│  │
│  ├────────────┼─────────────────┼────┼────┼────────┼───┤  │
│  │ [A] Nguyễn │ student1@...    │ 4  │ 12 │🟢 Hoạt │⋮ │  │
│  │ [B] Trần   │ student2@...    │ 5  │ 15 │🟢 Hoạt │⋮ │  │
│  │ [C] Lê     │ student3@...    │ 3  │ 8  │⚪ Không│⋮ │  │
│  │ [D] Phạm   │ student4@...    │ 4  │ 11 │🟢 Hoạt │⋮ │  │
│  └────────────┴─────────────────┴────┴────┴────────┴───┘  │
└─────────────────────────────────────────────────────────────┘

Table: Full width, striped rows on hover
```

---

## 📱 Responsive Design

### Breakpoints

```css
/* Tailwind Breakpoints */
sm:  640px   /* Small tablets */
md:  768px   /* Tablets */
lg:  1024px  /* Small laptops */
xl:  1280px  /* Desktops */
2xl: 1536px  /* Large screens */
```

### Responsive Behavior

**Mobile (< 768px):**
- Sidebar: Hidden, hamburger menu
- Stats: 1 column (stack vertically)
- Subject cards: 1 column
- Main content: Single column
- Padding: 16px (p-4)

**Tablet (768px - 1023px):**
- Sidebar: Hidden, hamburger menu
- Stats: 2 columns
- Subject cards: 2 columns
- Main content: Still stacked
- Padding: 24px (p-6)

**Desktop (>= 1024px):**
- Sidebar: Fixed 256px width
- Stats: 4 columns
- Subject cards: 3 columns
- Main content: 2 columns (2/3 + 1/3)
- Padding: 32px (p-8)

---

## ✍️ Typography & Spacing

### Font Families

```css
/* Default system fonts */
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", 
             Roboto, "Helvetica Neue", Arial, sans-serif;
```

### Font Sizes

```css
h1: 1.5rem (24px)    /* Page titles */
h2: 1.25rem (20px)   /* Section titles */
h3: 1.125rem (18px)  /* Card titles */
h4: 1rem (16px)      /* Subsection titles */
p:  1rem (16px)      /* Body text */

text-xs:   0.75rem (12px)   /* Small labels */
text-sm:   0.875rem (14px)  /* Secondary text */
text-base: 1rem (16px)      /* Base */
text-lg:   1.125rem (18px)  /* Emphasis */
text-xl:   1.25rem (20px)   /* Headings */
text-2xl:  1.5rem (24px)    /* Large numbers */
text-3xl:  1.875rem (30px)  /* Hero text */
```

### Font Weights

```css
--font-weight-normal: 400  /* Body text */
--font-weight-medium: 500  /* Headings, buttons, labels */
```

### Spacing Scale

```css
/* Tailwind spacing (rem) */
0:   0
1:   0.25rem (4px)    /* Tiny gaps */
2:   0.5rem (8px)     /* Small gaps */
3:   0.75rem (12px)   /* Medium gaps */
4:   1rem (16px)      /* Default gap */
5:   1.25rem (20px)
6:   1.5rem (24px)    /* Card padding */
8:   2rem (32px)      /* Page padding */
10:  2.5rem (40px)
12:  3rem (48px)      /* Section spacing */
16:  4rem (64px)      /* Large spacing */
```

**Common Usage:**
- Gap between cards: `gap-4` or `gap-6` (16-24px)
- Card padding: `p-6` (24px)
- Page padding: `p-4 lg:p-8` (16px mobile, 32px desktop)
- Section margin: `mb-6` (24px)

---

### Border Radius

```css
--radius: 0.625rem (10px)  /* Default */

/* Variations: */
rounded-sm:   calc(var(--radius) - 4px)  /* 6px - Small elements */
rounded-md:   calc(var(--radius) - 2px)  /* 8px - Inputs */
rounded-lg:   var(--radius)              /* 10px - Cards */
rounded-xl:   calc(var(--radius) + 4px)  /* 14px - Large cards */
rounded-2xl:  1rem (16px)                /* Logo container */
rounded-full: 50%                        /* Avatars, badges */
```

---

### Shadows

```css
/* Box Shadows */
shadow-sm:  0 1px 2px rgba(0,0,0,0.05)     /* Subtle */
shadow:     0 1px 3px rgba(0,0,0,0.1)      /* Default cards */
shadow-md:  0 4px 6px rgba(0,0,0,0.1)      /* Elevated */
shadow-lg:  0 10px 15px rgba(0,0,0,0.1)    /* Hover cards */
shadow-xl:  0 20px 25px rgba(0,0,0,0.1)    /* Modals */

/* Usage: */
Card default: shadow
Card hover: shadow-lg
Modals/Dialogs: shadow-xl
```

---

## 🎯 Color Usage Guidelines

### Do's ✅

1. **Consistency màu theo ngữ nghĩa:**
   - Blue: Thông tin, học tập, môn học
   - Green: Thành công, hoàn thành, tiến độ tốt
   - Orange: Cảnh báo, thời gian, deadline
   - Red: Lỗi, quan trọng, quá hạn
   - Purple: Đặc biệt, premium features

2. **Contrast tốt:**
   - Text trên background trắng: >= #374151 (gray-700)
   - Icon trên màu nền: Dùng màu 600 (vd: blue-600)
   - Badge text: Dùng màu 700 trên background 100

3. **Hierarchy rõ ràng:**
   - Primary actions: Màu đậm (blue-600, black)
   - Secondary: Outline, ghost buttons
   - Tertiary: Text links

### Don'ts ❌

1. ❌ Không dùng quá nhiều màu trong 1 component
2. ❌ Không dùng màu quá sáng cho text (#FFFFFF trên bg trắng)
3. ❌ Không trộn lẫn ý nghĩa màu (vd: dùng red cho success)
4. ❌ Không dùng gradient ở quá nhiều chỗ (chỉ logo + special elements)

---

## 🔍 Accessibility (A11y)

```css
/* Focus states */
focus-visible:outline-ring/50

/* Minimum contrast ratios */
Text (>= 16px): 4.5:1
Large text (>= 24px): 3:1
UI components: 3:1

/* Color blind friendly */
- Không dựa hoàn toàn vào màu
- Có icons, labels kèm theo
- Dùng patterns nếu cần
```

---

## 📦 Component Sizing

```css
/* Common element sizes */

Avatar:
  - Small: 32px (w-8 h-8)
  - Medium: 40px (w-10 h-10)
  - Large: 48px (w-12 h-12)

Icon:
  - Small: 16px (w-4 h-4)
  - Medium: 20px (w-5 h-5)
  - Large: 24px (w-6 h-6)

Button:
  - Height: 40px (h-10)
  - Padding: px-4 (16px horizontal)
  - Small: h-8 px-3
  - Large: h-12 px-6

Input:
  - Height: 40px (h-10)
  - Padding: px-3 (12px)

Card:
  - Min-height: Auto
  - Padding: p-6 (24px)
  - Border-radius: rounded-lg (10px)

Modal/Dialog:
  - Max-width: 500px (sm:max-w-[500px])
  - Padding: p-6
```

---

## 🎨 Quick Reference: Color Mapping

```javascript
// Component Color Map
{
  logo: "gradient(blue-600 → purple-600)",
  
  stats: {
    subjects: "blue-100 bg + blue-600 icon",
    assignments: "green-100 bg + green-600 icon",
    hours: "orange-100 bg + orange-600 icon",
    progress: "purple-100 bg + purple-600 icon"
  },
  
  subjects: {
    math: "#3B82F6",      // blue-600
    web: "#10B981",       // green-600
    database: "#F59E0B",  // orange-600
    english: "#EF4444"    // red-600
  },
  
  status: {
    active: "blue-100 bg + blue-700 text",
    pending: "gray-100 bg + gray-700 text",
    completed: "green-100 bg + green-700 text",
    overdue: "red-600 text"
  },
  
  priority: {
    high: "red-100 bg + red-700 text",
    medium: "yellow-100 bg + yellow-700 text",
    low: "green-100 bg + green-700 text"
  }
}
```

---

## 💡 Tips Cho Developers

1. **Sử dụng Tailwind utility classes:**
   ```jsx
   // ✅ Good
   <div className="bg-blue-100 text-blue-600">
   
   // ❌ Avoid inline styles
   <div style={{background: '#DBEAFE'}}>
   ```

2. **Consistent spacing:**
   ```jsx
   // Dùng gap thay vì margin cho grids
   <div className="grid grid-cols-4 gap-4">
   
   // Dùng space-y cho vertical stacks
   <div className="space-y-4">
   ```

3. **Responsive patterns:**
   ```jsx
   // Mobile first
   <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
   ```

4. **Color variables:**
   ```jsx
   // Nếu cần dynamic colors:
   style={{ backgroundColor: `${color}20` }} // 20% opacity
   style={{ color: color }}
   ```

---

**Document Version:** 1.0  
**Last Updated:** 30/10/2025  
**Maintained by:** StudyHub Design Team
