# Customer Logs Feature - Flow & Implementation Guide

## 📋 Overview
Customer Logs is an **audit trail/logging system** that tracks all customer activities across the loyalty rewards platform. It provides a comprehensive history of customer interactions, transactions, and actions for analytics, compliance, and customer service.

---

## 🎯 Business Logic & Flow Understanding

### **What Should Be Logged?**

Customer Logs should track **all customer-related activities** including:

#### **1. Points Activities:**
- ✅ Points earned (from Point Awards)
- ✅ Points redeemed (for offers/rewards)
- ✅ Points expired
- ✅ Points adjusted (admin corrections)

#### **2. Spin Activities:**
- ✅ Spin performed
- ✅ Spin result (won points/offer/nothing)
- ✅ Spin eligibility check

#### **3. Offer Activities:**
- ✅ Offer viewed
- ✅ Offer redeemed
- ✅ Offer expired

#### **4. Scan Activities:**
- ✅ QR code scanned at site
- ✅ Check-in at location
- ✅ Visit recorded

#### **5. Profile Activities:**
- ✅ Profile updated
- ✅ Password changed
- ✅ Email verified
- ✅ Account created

#### **6. Transaction Activities:**
- ✅ Login
- ✅ Logout
- ✅ Session started

---

## 🔄 Customer Logs Flow

### **Scenario 1: Customer Earns Points**
```
1. Admin creates Point Award → Award saved
2. SYSTEM automatically creates Customer Log entry:
   - Action Type: "point_earned"
   - Customer: John Doe
   - Points: 50
   - Site: Store A
   - Merchant: ABC Corp
   - Description: "Awarded 50 points"
   - Timestamp: Auto
```

### **Scenario 2: Customer Performs Spin**
```
1. Customer spins wheel → Spin History saved
2. SYSTEM automatically creates Customer Log entry:
   - Action Type: "spin_completed"
   - Customer: John Doe
   - Result: "points" / "offer" / "nothing"
   - Points Won: 50 (if applicable)
   - Offer Won: "Weekend Special" (if applicable)
   - Site: Store A
   - Merchant: ABC Corp
   - Timestamp: Auto
```

### **Scenario 3: Customer Redeems Offer**
```
1. Customer redeems offer → Offer redeemed
2. SYSTEM automatically creates Customer Log entry:
   - Action Type: "offer_redeemed"
   - Customer: John Doe
   - Offer: "Weekend Special"
   - Points Used: 100
   - Site: Store A
   - Merchant: ABC Corp
   - Timestamp: Auto
```

### **Scenario 4: Customer Scans QR Code**
```
1. Customer scans QR code at site → Scan recorded
2. SYSTEM automatically creates Customer Log entry:
   - Action Type: "qr_code_scanned"
   - Customer: John Doe
   - Site: Store A
   - Merchant: ABC Corp
   - Scan Time: Auto
   - Location: GPS coordinates (optional)
```

### **Scenario 5: Manual Log Entry (Admin)**
```
1. Admin creates manual log entry
2. Admin fills form:
   - Customer: Select customer
   - Action Type: Select type
   - Description: Enter notes
   - Site/Merchant: Auto or manual
3. Log entry saved
```

---

## 📊 Database Structure Proposal

### **Table: `customer_logs`**

```php
Required Fields:
├── id (UUID, Primary Key)
├── merchant_id (UUID, Foreign Key) - Merchant context
├── site_id (UUID, Foreign Key, Nullable) - Site location (if applicable)
├── user_id (Foreign Key) - Customer who performed action
│
├── action_type (String) - Type of action performed
│   Options: 
│   - 'point_earned' - Customer earned points
│   - 'point_redeemed' - Customer redeemed points
│   - 'point_expired' - Points expired
│   - 'point_adjusted' - Admin adjusted points
│   - 'spin_completed' - Spin performed
│   - 'offer_viewed' - Offer viewed
│   - 'offer_redeemed' - Offer redeemed
│   - 'qr_code_scanned' - QR code scanned
│   - 'check_in' - Site check-in
│   - 'profile_updated' - Profile changed
│   - 'login' - Customer logged in
│   - 'logout' - Customer logged out
│   - 'account_created' - Account created
│   - 'custom' - Custom/admin log entry
│
├── action_category (String) - Category for grouping
│   Options: 'points', 'spins', 'offers', 'scans', 'profile', 'system'
│
├── description (Text) - Human-readable description of action
│   Example: "Earned 50 points from Point Award"
│   Example: "Redeemed offer: Weekend Special (100 points)"
│
├── points_affected (Integer, Nullable) - Points involved in action
│   - Positive: Points earned
│   - Negative: Points redeemed/expired
│   - NULL: No points involved
│
├── points_balance_before (Integer, Nullable) - Points before action
├── points_balance_after (Integer, Nullable) - Points after action
│
├── related_model_type (String, Nullable) - Related model class
│   Examples: 'App\Models\PointAward', 'App\Models\SpinHistory', 'App\Models\Offer'
│
├── related_model_id (UUID/String, Nullable) - Related model ID
│   - Links to the specific record (e.g., point_award_id, spin_history_id)
│
├── metadata (JSON, Nullable) - Additional data
│   Example: {
│     "offer_id": "uuid",
│     "offer_title": "Weekend Special",
│     "spin_result": "points",
│     "ip_address": "192.168.1.1",
│     "device_info": {...}
│   }
│
├── performed_by_id (Foreign Key, Nullable) - Who performed action
│   - If NULL: Customer performed
│   - If set: Admin/staff who performed action
│
├── ip_address (String, Nullable) - IP address of action
├── user_agent (String, Nullable) - Browser/device info
├── location_data (JSON, Nullable) - GPS/location data (for scans)
│
└── timestamps (created_at, updated_at)
```

---

## 🔗 Integration Points

### **Auto-Logging: Event Listeners/Observers**

#### **1. PointAward Model Events:**
```php
// When PointAward is created
PointAward::created(function ($pointAward) {
    CustomerLog::create([
        'merchant_id' => $pointAward->merchant_id,
        'site_id' => $pointAward->site_id,
        'user_id' => $pointAward->user_id,
        'action_type' => 'point_earned',
        'action_category' => 'points',
        'description' => "Earned {$pointAward->points_earned} points from Point Award",
        'points_affected' => $pointAward->points_earned,
        'related_model_type' => PointAward::class,
        'related_model_id' => $pointAward->id,
        // Calculate points balance before/after
    ]);
});
```

#### **2. SpinHistory Model Events:**
```php
// When SpinHistory is created
SpinHistory::created(function ($spinHistory) {
    $description = match($spinHistory->spin_result_type) {
        'points' => "Won {$spinHistory->points_earned} points from spin wheel",
        'offer' => "Won offer: {$spinHistory->offer->title} from spin wheel",
        'nothing' => "Spin completed - no reward",
        default => "Spin completed"
    };
    
    CustomerLog::create([
        'merchant_id' => $spinHistory->merchant_id,
        'site_id' => $spinHistory->site_id,
        'user_id' => $spinHistory->user_id,
        'action_type' => 'spin_completed',
        'action_category' => 'spins',
        'description' => $description,
        'points_affected' => $spinHistory->points_earned ?? 0,
        'related_model_type' => SpinHistory::class,
        'related_model_id' => $spinHistory->id,
        'metadata' => [
            'spin_result_type' => $spinHistory->spin_result_type,
            'spin_number' => $spinHistory->spin_number,
            'is_eligible' => $spinHistory->is_eligible,
        ],
    ]);
});
```

#### **3. Offer Redemption (when implemented):**
```php
// When customer redeems offer
OfferRedeemed::dispatch($customer, $offer, $pointsUsed);

// Listener creates log
CustomerLog::create([
    'action_type' => 'offer_redeemed',
    'description' => "Redeemed offer: {$offer->title} ({$pointsUsed} points)",
    'points_affected' => -$pointsUsed, // Negative for redemption
    // ...
]);
```

---

## 📱 Admin Dashboard Views

### **List View (`customer-logs.index`):**
```
Columns:
- Date/Time
- Customer Name
- Action Type (badge)
- Category
- Description
- Points (if applicable)
- Site/Merchant
- Actions (View)
```

### **Filters:**
- By Customer (search/select)
- By Action Type
- By Category
- By Date Range
- By Merchant
- By Site
- By Points (earned/redeemed)

### **Detail View (`customer-logs.show`):**
- Full log entry details
- Related record link (if applicable)
- Metadata (JSON formatted)
- Timeline view (for customer)

---

## 🎯 Key Questions to Clarify:

### **1. Auto-Logging vs Manual:**
- ❓ Should logs be **automatically created** when actions happen?
- ❓ Or only **manually** by admins?
- ✅ **Recommendation**: Auto-log major actions, allow manual for notes/corrections

### **2. Points Balance Tracking:**
- ❓ Should we track **running balance** (before/after)?
- ✅ **Recommendation**: Yes, for audit trail

### **3. Integration:**
- ❓ Should Customer Logs **automatically log** Point Awards and Spin History?
- ✅ **Recommendation**: Yes, via Model Events/Observers

### **4. Customer Visibility:**
- ❓ Can customers see **their own logs**?
- ✅ **Recommendation**: Yes, in customer dashboard

### **5. Log Retention:**
- ❓ How long to keep logs?
- ❓ Should old logs be archived?

---

## 🚀 Implementation Approach

### **Option 1: Simple Logging (Recommended for Start)**
- Create log entries **manually** or via **helper methods**
- Store all action details
- View/Filter/Export logs

### **Option 2: Event-Driven Auto-Logging**
- Use **Model Events** or **Observers**
- Automatically create logs on model actions
- More complex but fully automated

### **Option 3: Hybrid Approach (Best)**
- Auto-log major actions (Point Awards, Spins)
- Allow manual log entries for notes/custom events
- Provides flexibility

---

## 📋 Next Steps:

1. **Confirm Requirements:**
   - Which actions to log?
   - Auto vs Manual logging?
   - Points balance tracking needed?

2. **Design Database:**
   - Finalize fields
   - Create migration

3. **Implement Model & Repository:**
   - CustomerLog model
   - Relationships
   - Filtering logic

4. **Create Controllers & Views:**
   - List/Show pages
   - Filters

5. **Integrate Auto-Logging:**
   - Model Events/Observers
   - Helper methods

6. **Testing:**
   - Verify auto-logging works
   - Test filters
   - Test manual entries

---

**Please confirm:**
1. Should Customer Logs auto-log Point Awards and Spin History?
2. Do you need points balance tracking (before/after)?
3. Should admins be able to create manual log entries?
4. Which action types are most important to track first?

