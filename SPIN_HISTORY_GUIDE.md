# Spin History Feature - Implementation Guide

## 📋 Overview
The Spin History feature tracks customer spins on a loyalty reward wheel. Customers can spin after a certain number of days (configured per merchant via `spin_after_days`).

---

## 🎯 Business Logic Understanding

### Spin Mechanism:
1. **Customer Eligibility**: Customers can spin a wheel every X days (set in `merchant.spin_after_days`)
2. **Spin Action**: Customer performs a spin action on the frontend/mobile app
3. **Reward Result**: Spin lands on a prize (points, offer, or nothing)
4. **History Tracking**: Each spin is recorded for analytics and compliance

### Example Flow:
- Merchant sets `spin_after_days = 7`
- Customer spins on Day 1 → Wins 50 points → Recorded
- Customer tries to spin on Day 3 → Blocked (only 2 days passed)
- Customer spins on Day 8 → Wins an Offer → Recorded
- Admin views all spins in Spin History

---

## 📊 Required Database Fields (Migration Structure)

### Table: `spin_histories`

```php
Required Fields:
├── id (UUID, Primary Key) - Unique spin record identifier
├── merchant_id (UUID, Foreign Key) - Which merchant the spin belongs to
├── site_id (UUID, Foreign Key) - Which site/location the spin happened at
├── user_id (Foreign Key) - Customer who performed the spin
│
├── spin_result_type (Enum/String) - What type of reward was won
│   Options: 'points', 'offer', 'nothing', 'discount', etc.
│
├── reward_value (Integer/Decimal) - Numerical value of reward
│   - If points: e.g., 50, 100, 200
│   - If offer: NULL or offer_id reference
│   - If nothing: 0
│
├── points_earned (Integer, Default: 0) - Points won from spin (if applicable)
│
├── offer_id (UUID, Nullable) - Reference to won offer (if result_type = 'offer')
│
├── spin_number (Integer) - Sequential spin number for this customer at this merchant
│   - Helps track: "This was customer's 5th spin"
│
├── is_eligible (Boolean) - Whether spin was allowed based on spin_after_days rule
│   - true: Valid spin within rules
│   - false: Admin override or exception
│
├── last_spin_date (Date) - When customer last spun (for validation)
│
├── notes (Text, Nullable) - Admin notes or spin details
│
├── ip_address (String, Nullable) - For fraud detection
│
├── device_info (String/JSON, Nullable) - Mobile/Web device info
│
└── timestamps (created_at, updated_at) - When spin occurred
```

---

## 🗂️ Relationships Needed

### 1. SpinHistory Model Relationships:
```php
- belongsTo(User::class) // Customer who spun
- belongsTo(Merchant::class) // Merchant
- belongsTo(Site::class) // Site location
- belongsTo(Offer::class, 'offer_id') // Won offer (nullable)
```

---

## 📝 Key Variables Breakdown

### **Essential Variables:**

1. **`merchant_id`** (UUID)
   - **Purpose**: Links spin to merchant
   - **Source**: Derived from `site_id` (site belongs to merchant)
   - **Why needed**: Filter spins by merchant, apply merchant rules

2. **`site_id`** (UUID, Required)
   - **Purpose**: Specific location where spin occurred
   - **Source**: Selected from form or app location
   - **Why needed**: Track which physical/virtual location

3. **`user_id`** (Integer, Required)
   - **Purpose**: Customer who performed the spin
   - **Source**: Authenticated customer user
   - **Why needed**: Track customer spin history, enforce eligibility

4. **`spin_result_type`** (Enum/String, Required)
   - **Purpose**: Type of reward won
   - **Possible Values**: 
     - `'points'` - Customer won points
     - `'offer'` - Customer won an offer/reward
     - `'nothing'` - No reward this spin
     - `'discount'` - Discount percentage
   - **Why needed**: Filter and categorize spin results

5. **`points_earned`** (Integer, Default: 0)
   - **Purpose**: Points won if result_type is 'points'
   - **Example**: 50, 100, 200 points
   - **Why needed**: Calculate total points from spins

6. **`offer_id`** (UUID, Nullable)
   - **Purpose**: Reference to won offer (if result_type = 'offer')
   - **Why needed**: Link to offer details, track which offers are popular

7. **`reward_value`** (Integer/Decimal, Nullable)
   - **Purpose**: Generic reward value
   - **Use**: Can store discount %, cash value, etc.
   - **Why needed**: Display reward value in different formats

### **Tracking Variables:**

8. **`spin_number`** (Integer)
   - **Purpose**: Sequential count of spins for this customer at merchant
   - **Example**: 1st spin, 2nd spin, 5th spin
   - **Calculation**: `MAX(spin_number) + 1` for customer+merchant
   - **Why needed**: Analytics, "Your 10th spin!" messaging

9. **`is_eligible`** (Boolean, Default: true)
   - **Purpose**: Whether spin followed eligibility rules
   - **Logic**: Check if `spin_after_days` rule was followed
   - **Why needed**: Track rule violations, admin overrides

10. **`last_spin_date`** (Date)
    - **Purpose**: When customer last spun at this merchant
    - **Source**: Previous spin's `created_at` date
    - **Why needed**: Calculate days since last spin for eligibility

### **Additional Variables (Optional but Recommended):**

11. **`notes`** (Text, Nullable)
    - **Purpose**: Admin notes, special circumstances
    - **Example**: "Manual override", "Promotional spin"

12. **`ip_address`** (String, Nullable)
    - **Purpose**: Fraud detection, security
    - **Why needed**: Track unusual patterns

13. **`device_info`** (String/JSON, Nullable)
    - **Purpose**: Mobile app, web browser info
    - **Example**: `{"platform": "iOS", "app_version": "1.2.3"}`

---

## 🔍 Filtering Requirements (Based on PointAward Pattern)

Spin History should support filters:

1. **By Merchant** (`merchant_id`)
2. **By Site** (`site_id`)
3. **By Customer** (Search by `user_id` or name/email)
4. **By Date Range** (`date_from`, `date_to`)
5. **By Result Type** (`spin_result_type`)
6. **By Eligibility** (`is_eligible`)

---

## 🎨 Display Requirements (Views)

### List Page (`spin-history.index`):
- Table with columns:
  - Date/Time
  - Customer Name
  - Site
  - Merchant
  - Result Type
  - Reward (Points/Offer)
  - Spin Number
  - Status (Eligible/Not Eligible)
  - Actions (View/Edit/Delete)

### Show Page (`spin-history.show`):
- Full spin details
- Customer information
- Site/Merchant info
- Reward details
- Eligibility status
- Notes

### Create/Edit Pages:
- Form fields (if admin manually creates):
  - Site (dropdown)
  - Customer (dropdown/search)
  - Result Type (dropdown)
  - Points Earned (if type = points)
  - Offer (if type = offer)
  - Notes
  - Eligibility checkbox

---

## 🔐 Permission Requirements

Already configured in system:
- `view_spin_history`
- `add_spin_history`
- `edit_spin_history`
- `delete_spin_history`

---

## 📋 Implementation Checklist

Before starting implementation:

- [x] Understand business logic (spin_after_days rule)
- [x] Define required database fields
- [x] Understand relationships (User, Merchant, Site, Offer)
- [x] Review PointAward pattern for consistency
- [x] Identify filtering requirements
- [ ] Create migration file
- [ ] Create SpinHistory model
- [ ] Create SpinHistoryDto
- [ ] Create SpinHistoryRepository
- [ ] Create SpinHistoryController
- [ ] Create SpinHistoryRequest (validation)
- [ ] Create views (list, show, create, edit)
- [ ] Add routes
- [ ] Test eligibility logic

---

## ⚠️ Important Considerations

1. **Eligibility Validation**:
   - Before allowing spin, check:
     ```php
     $lastSpin = SpinHistory::where('user_id', $userId)
         ->where('merchant_id', $merchantId)
         ->latest('created_at')
         ->first();
     
     $daysSinceLastSpin = $lastSpin 
         ? now()->diffInDays($lastSpin->created_at) 
         : 999; // First spin ever
     
     $merchant = Merchant::find($merchantId);
     $isEligible = $daysSinceLastSpin >= $merchant->spin_after_days;
     ```

2. **Spin Number Calculation**:
   ```php
     $lastSpin = SpinHistory::where('user_id', $userId)
         ->where('merchant_id', $merchantId)
         ->orderBy('spin_number', 'desc')
         ->first();
     
     $spinNumber = $lastSpin ? $lastSpin->spin_number + 1 : 1;
     ```

3. **Multi-tenancy Scoping**:
   - Use `HasMerchantScope` trait (like PointAward)
   - Filter by accessible merchants/sites based on user role

4. **Points Integration**:
   - If spin wins points, you may need to:
     - Create a PointAward record separately, OR
     - Just track in spin_history and award points elsewhere

---

## 🚀 Next Steps

1. Review this guide and confirm variables
2. Create migration with all fields
3. Implement model, repository, controller following PointAward pattern
4. Create views
5. Test eligibility logic
6. Add to routes

---

**Questions to Clarify Before Implementation:**
1. When customer spins and wins points, should we automatically create a PointAward record?
2. Can admins manually create spin history records?
3. Can customers see their own spin history, or only admins?
4. Should there be a maximum spin limit per day/week/month?
5. Can customers spin multiple times on same day if eligible?


