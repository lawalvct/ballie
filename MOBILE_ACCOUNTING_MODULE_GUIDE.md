# React Native Accounting Module - Complete Guide

> **Note:** This guide focuses on UI/UX implementation with dummy data. API integration will be covered separately.

## 📋 Table of Contents

1. [Module Overview](#module-overview)
2. [Navigation Structure](#navigation-structure)
3. [Accounting Dashboard](#1-accounting-dashboard)
4. [Account Groups Management](#2-account-groups-management)
5. [Ledger Accounts (Chart of Accounts)](#3-ledger-accounts-chart-of-accounts)
6. [Voucher Management](#4-voucher-management)
7. [Bank Accounts](#5-bank-accounts)
8. [Bank Reconciliation](#6-bank-reconciliation)
9. [Design System](#design-system)
10. [Dummy Data Structures](#dummy-data-structures)

---

## Module Overview

The Accounting Module is the core financial management system with these key features:

### Core Capabilities
- **Double-Entry Bookkeeping** - Every transaction affects at least two accounts
- **Multi-Currency Support** - Handle different currencies (NGN, USD, EUR, etc.)
- **Voucher System** - Standardized transaction recording
- **Real-time Balance Tracking** - Live account balances
- **Financial Reporting** - P&L, Balance Sheet, Trial Balance

### User Roles & Permissions
- **Owner** - Full access to all accounting features
- **Accountant** - Create/edit vouchers, view reports
- **Manager** - View reports, approve transactions
- **Bookkeeper** - Data entry, basic vouchers
- **Viewer** - Read-only access

---

## Navigation Structure

### Bottom Tab Navigator (Main App)
```typescript
// src/navigation/MainNavigator.tsx
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';

<Tab.Navigator>
  <Tab.Screen name="Dashboard" component={DashboardStack} />
  <Tab.Screen name="Accounting" component={AccountingStack} /> {/* Focus */}
  <Tab.Screen name="Inventory" component={InventoryStack} />
  <Tab.Screen name="CRM" component={CRMStack} />
  <Tab.Screen name="More" component={MoreStack} />
</Tab.Navigator>
```

### Accounting Stack Navigator
```typescript
// src/navigation/AccountingNavigator.tsx
import { createStackNavigator } from '@react-navigation/stack';

const AccountingStack = createStackNavigator();

export default function AccountingNavigator() {
  return (
    <AccountingStack.Navigator
      screenOptions={{
        headerStyle: { backgroundColor: '#2b6399' },
        headerTintColor: '#fff',
        headerTitleStyle: { fontWeight: 'bold' },
      }}
    >
      {/* Dashboard */}
      <AccountingStack.Screen
        name="AccountingDashboard"
        component={AccountingDashboardScreen}
        options={{ title: 'Accounting' }}
      />

      {/* Account Groups */}
      <AccountingStack.Screen
        name="AccountGroups"
        component={AccountGroupsScreen}
        options={{ title: 'Account Groups' }}
      />
      <AccountingStack.Screen
        name="AccountGroupCreate"
        component={AccountGroupFormScreen}
        options={{ title: 'New Account Group' }}
      />
      <AccountingStack.Screen
        name="AccountGroupEdit"
        component={AccountGroupFormScreen}
        options={{ title: 'Edit Account Group' }}
      />

      {/* Ledger Accounts (COA) */}
      <AccountingStack.Screen
        name="LedgerAccounts"
        component={LedgerAccountsScreen}
        options={{ title: 'Chart of Accounts' }}
      />
      <AccountingStack.Screen
        name="LedgerAccountCreate"
        component={LedgerAccountFormScreen}
        options={{ title: 'New Ledger Account' }}
      />
      <AccountingStack.Screen
        name="LedgerAccountDetail"
        component={LedgerAccountDetailScreen}
        options={{ title: 'Account Details' }}
      />
      <AccountingStack.Screen
        name="LedgerStatement"
        component={LedgerStatementScreen}
        options={{ title: 'Account Statement' }}
      />

      {/* Vouchers */}
      <AccountingStack.Screen
        name="VouchersList"
        component={VouchersListScreen}
        options={{ title: 'Vouchers' }}
      />
      <AccountingStack.Screen
        name="VoucherCreate"
        component={VoucherCreateScreen}
        options={{ title: 'Create Voucher' }}
      />
      <AccountingStack.Screen
        name="VoucherDetail"
        component={VoucherDetailScreen}
        options={{ title: 'Voucher Details' }}
      />

      {/* Specific Voucher Types */}
      <AccountingStack.Screen
        name="SalesInvoice"
        component={SalesInvoiceScreen}
        options={{ title: 'Sales Invoice' }}
      />
      <AccountingStack.Screen
        name="PurchaseInvoice"
        component={PurchaseInvoiceScreen}
        options={{ title: 'Purchase Invoice' }}
      />
      <AccountingStack.Screen
        name="PaymentVoucher"
        component={PaymentVoucherScreen}
        options={{ title: 'Payment' }}
      />
      <AccountingStack.Screen
        name="ReceiptVoucher"
        component={ReceiptVoucherScreen}
        options={{ title: 'Receipt' }}
      />
      <AccountingStack.Screen
        name="JournalEntry"
        component={JournalEntryScreen}
        options={{ title: 'Journal Entry' }}
      />
      <AccountingStack.Screen
        name="ContraVoucher"
        component={ContraVoucherScreen}
        options={{ title: 'Contra Entry' }}
      />
      <AccountingStack.Screen
        name="CreditNote"
        component={CreditNoteScreen}
        options={{ title: 'Credit Note' }}
      />
      <AccountingStack.Screen
        name="DebitNote"
        component={DebitNoteScreen}
        options={{ title: 'Debit Note' }}
      />

      {/* Bank Accounts */}
      <AccountingStack.Screen
        name="BankAccounts"
        component={BankAccountsScreen}
        options={{ title: 'Bank Accounts' }}
      />
      <AccountingStack.Screen
        name="BankAccountDetail"
        component={BankAccountDetailScreen}
        options={{ title: 'Bank Account' }}
      />

      {/* Reconciliation */}
      <AccountingStack.Screen
        name="Reconciliation"
        component={ReconciliationScreen}
        options={{ title: 'Bank Reconciliation' }}
      />
      <AccountingStack.Screen
        name="ReconciliationDetail"
        component={ReconciliationDetailScreen}
        options={{ title: 'Reconcile' }}
      />
    </AccountingStack.Navigator>
  );
}
```

---

## 1. Accounting Dashboard

### Screen: AccountingDashboardScreen

**Purpose:** Overview of financial health with key metrics, charts, and quick actions.

### UI Layout

```
┌─────────────────────────────────────┐
│ ← Accounting            [Filter] 🔔 │
├─────────────────────────────────────┤
│  📊 Financial Overview              │
│                                     │
│  ┌─────────┐ ┌─────────┐          │
│  │Revenue  │ │ Expenses│          │
│  │₦2.5M    │ │ ₦1.2M   │          │
│  │↑ 12.5%  │ │↓ 5.3%   │          │
│  └─────────┘ └─────────┘          │
│                                     │
│  ┌─────────┐ ┌─────────┐          │
│  │ Profit  │ │Outstanding│        │
│  │₦1.3M    │ │ ₦450K   │          │
│  │↑ 25.8%  │ │ 12 Items│          │
│  └─────────┘ └─────────┘          │
│                                     │
│  📈 Revenue vs Expenses (6M)        │
│  ┌───────────────────────────────┐ │
│  │   Chart Area                  │ │
│  │   [Line Chart Here]           │ │
│  └───────────────────────────────┘ │
│                                     │
│  💰 Quick Actions                   │
│  [Invoice] [Payment] [Receipt]     │
│  [Journal] [More Actions ▼]        │
│                                     │
│  📝 Recent Transactions             │
│  ┌─────────────────────────────┐   │
│  │ INV-001  Sales  +₦45,000    │   │
│  │ PAY-052  Expense -₦12,000   │   │
│  │ REC-089  Receipt +₦28,000   │   │
│  └─────────────────────────────┘   │
│                                     │
│  📊 Voucher Summary                 │
│  Sales: 45 | Purchase: 32         │
│  Payment: 28 | Receipt: 35        │
└─────────────────────────────────────┘
```

### Component Structure

```typescript
// src/screens/Accounting/AccountingDashboardScreen.tsx
import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Dimensions,
} from 'react-native';
import { LineChart } from 'react-native-chart-kit';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';

// Dummy Data
const dummyDashboardData = {
  metrics: {
    totalRevenue: 2500000,
    totalExpenses: 1200000,
    profit: 1300000,
    outstandingInvoices: 450000,
    pendingInvoicesCount: 12,
    revenueChange: { percentage: 12.5, direction: 'up' },
    expenseChange: { percentage: 5.3, direction: 'down' },
    profitChange: { percentage: 25.8, direction: 'up' },
  },
  chartData: {
    labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    revenue: [1800000, 2100000, 1950000, 2300000, 2450000, 2500000],
    expenses: [950000, 1100000, 1050000, 1150000, 1180000, 1200000],
  },
  recentTransactions: [
    {
      id: 1,
      voucherNumber: 'INV-001',
      type: 'Sales Invoice',
      amount: 45000,
      date: '2025-12-28',
      status: 'posted',
      isIncome: true,
    },
    {
      id: 2,
      voucherNumber: 'PAY-052',
      type: 'Payment',
      amount: 12000,
      date: '2025-12-27',
      status: 'posted',
      isIncome: false,
    },
    {
      id: 3,
      voucherNumber: 'REC-089',
      type: 'Receipt',
      amount: 28000,
      date: '2025-12-27',
      status: 'posted',
      isIncome: true,
    },
    {
      id: 4,
      voucherNumber: 'JV-023',
      type: 'Journal Entry',
      amount: 15000,
      date: '2025-12-26',
      status: 'posted',
      isIncome: false,
    },
    {
      id: 5,
      voucherNumber: 'PUR-078',
      type: 'Purchase Invoice',
      amount: 67000,
      date: '2025-12-25',
      status: 'draft',
      isIncome: false,
    },
  ],
  voucherSummary: {
    sales: 45,
    purchase: 32,
    payment: 28,
    receipt: 35,
    journal: 12,
    contra: 8,
  },
};

export default function AccountingDashboardScreen({ navigation }) {
  const [data, setData] = useState(dummyDashboardData);
  const [period, setPeriod] = useState('6m'); // 6m or 1y

  const formatCurrency = (amount: number) => {
    return `₦${(amount / 1000).toFixed(1)}K`;
  };

  const formatLargeCurrency = (amount: number) => {
    if (amount >= 1000000) {
      return `₦${(amount / 1000000).toFixed(1)}M`;
    }
    return `₦${(amount / 1000).toFixed(0)}K`;
  };

  return (
    <ScrollView style={styles.container}>
      {/* Header with Filter */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Financial Overview</Text>
        <TouchableOpacity style={styles.filterButton}>
          <Icon name="filter-variant" size={20} color="#2b6399" />
          <Text style={styles.filterText}>This Month</Text>
        </TouchableOpacity>
      </View>

      {/* Metrics Cards */}
      <View style={styles.metricsContainer}>
        {/* Revenue Card */}
        <View style={[styles.metricCard, styles.revenueCard]}>
          <View style={styles.metricHeader}>
            <Icon name="trending-up" size={24} color="#10b981" />
            <Text style={styles.metricLabel}>Revenue</Text>
          </View>
          <Text style={styles.metricValue}>
            {formatLargeCurrency(data.metrics.totalRevenue)}
          </Text>
          <View style={styles.metricChange}>
            <Icon
              name={data.metrics.revenueChange.direction === 'up' ? 'arrow-up' : 'arrow-down'}
              size={16}
              color="#10b981"
            />
            <Text style={styles.metricChangeText}>
              {data.metrics.revenueChange.percentage}% vs last month
            </Text>
          </View>
        </View>

        {/* Expenses Card */}
        <View style={[styles.metricCard, styles.expenseCard]}>
          <View style={styles.metricHeader}>
            <Icon name="trending-down" size={24} color="#ef4444" />
            <Text style={styles.metricLabel}>Expenses</Text>
          </View>
          <Text style={styles.metricValue}>
            {formatLargeCurrency(data.metrics.totalExpenses)}
          </Text>
          <View style={styles.metricChange}>
            <Icon
              name={data.metrics.expenseChange.direction === 'down' ? 'arrow-down' : 'arrow-up'}
              size={16}
              color="#10b981"
            />
            <Text style={styles.metricChangeText}>
              {data.metrics.expenseChange.percentage}% vs last month
            </Text>
          </View>
        </View>

        {/* Profit Card */}
        <View style={[styles.metricCard, styles.profitCard]}>
          <View style={styles.metricHeader}>
            <Icon name="currency-usd" size={24} color="#8b5cf6" />
            <Text style={styles.metricLabel}>Profit</Text>
          </View>
          <Text style={styles.metricValue}>
            {formatLargeCurrency(data.metrics.profit)}
          </Text>
          <View style={styles.metricChange}>
            <Icon name="arrow-up" size={16} color="#10b981" />
            <Text style={styles.metricChangeText}>
              {data.metrics.profitChange.percentage}% vs last month
            </Text>
          </View>
        </View>

        {/* Outstanding Card */}
        <View style={[styles.metricCard, styles.outstandingCard]}>
          <View style={styles.metricHeader}>
            <Icon name="clock-alert-outline" size={24} color="#f59e0b" />
            <Text style={styles.metricLabel}>Outstanding</Text>
          </View>
          <Text style={styles.metricValue}>
            {formatLargeCurrency(data.metrics.outstandingInvoices)}
          </Text>
          <Text style={styles.metricSubtext}>
            {data.metrics.pendingInvoicesCount} pending invoices
          </Text>
        </View>
      </View>

      {/* Chart Section */}
      <View style={styles.chartSection}>
        <View style={styles.chartHeader}>
          <Text style={styles.sectionTitle}>Revenue vs Expenses</Text>
          <View style={styles.periodSelector}>
            <TouchableOpacity
              style={[styles.periodButton, period === '6m' && styles.periodButtonActive]}
              onPress={() => setPeriod('6m')}
            >
              <Text style={[styles.periodText, period === '6m' && styles.periodTextActive]}>
                6M
              </Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.periodButton, period === '1y' && styles.periodButtonActive]}
              onPress={() => setPeriod('1y')}
            >
              <Text style={[styles.periodText, period === '1y' && styles.periodTextActive]}>
                1Y
              </Text>
            </TouchableOpacity>
          </View>
        </View>

        <LineChart
          data={{
            labels: data.chartData.labels,
            datasets: [
              {
                data: data.chartData.revenue.map(v => v / 1000000),
                color: () => '#10b981',
                strokeWidth: 2,
              },
              {
                data: data.chartData.expenses.map(v => v / 1000000),
                color: () => '#ef4444',
                strokeWidth: 2,
              },
            ],
            legend: ['Revenue', 'Expenses'],
          }}
          width={Dimensions.get('window').width - 40}
          height={220}
          chartConfig={{
            backgroundColor: '#ffffff',
            backgroundGradientFrom: '#ffffff',
            backgroundGradientTo: '#ffffff',
            decimalPlaces: 1,
            color: (opacity = 1) => `rgba(0, 0, 0, ${opacity})`,
            labelColor: (opacity = 1) => `rgba(0, 0, 0, ${opacity})`,
            style: { borderRadius: 16 },
            propsForDots: { r: '4', strokeWidth: '2' },
          }}
          bezier
          style={styles.chart}
        />
      </View>

      {/* Quick Actions */}
      <View style={styles.quickActionsSection}>
        <Text style={styles.sectionTitle}>Quick Actions</Text>
        <View style={styles.quickActionsGrid}>
          <TouchableOpacity
            style={[styles.quickActionButton, styles.invoiceButton]}
            onPress={() => navigation.navigate('SalesInvoice')}
          >
            <Icon name="receipt" size={24} color="#fff" />
            <Text style={styles.quickActionText}>Invoice</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.quickActionButton, styles.paymentButton]}
            onPress={() => navigation.navigate('PaymentVoucher')}
          >
            <Icon name="cash-minus" size={24} color="#fff" />
            <Text style={styles.quickActionText}>Payment</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.quickActionButton, styles.receiptButton]}
            onPress={() => navigation.navigate('ReceiptVoucher')}
          >
            <Icon name="cash-plus" size={24} color="#fff" />
            <Text style={styles.quickActionText}>Receipt</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.quickActionButton, styles.journalButton]}
            onPress={() => navigation.navigate('JournalEntry')}
          >
            <Icon name="notebook-edit" size={24} color="#fff" />
            <Text style={styles.quickActionText}>Journal</Text>
          </TouchableOpacity>
        </View>

        <TouchableOpacity
          style={styles.moreActionsButton}
          onPress={() => {/* Show bottom sheet with all actions */}}
        >
          <Text style={styles.moreActionsText}>More Actions</Text>
          <Icon name="chevron-down" size={20} color="#2b6399" />
        </TouchableOpacity>
      </View>

      {/* Recent Transactions */}
      <View style={styles.recentSection}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Recent Transactions</Text>
          <TouchableOpacity onPress={() => navigation.navigate('VouchersList')}>
            <Text style={styles.viewAllText}>View All</Text>
          </TouchableOpacity>
        </View>

        {data.recentTransactions.map((transaction) => (
          <TouchableOpacity
            key={transaction.id}
            style={styles.transactionItem}
            onPress={() => navigation.navigate('VoucherDetail', { id: transaction.id })}
          >
            <View style={styles.transactionLeft}>
              <Icon
                name={transaction.isIncome ? 'arrow-down-circle' : 'arrow-up-circle'}
                size={32}
                color={transaction.isIncome ? '#10b981' : '#ef4444'}
              />
              <View style={styles.transactionInfo}>
                <Text style={styles.transactionType}>{transaction.type}</Text>
                <Text style={styles.transactionNumber}>{transaction.voucherNumber}</Text>
                <Text style={styles.transactionDate}>{transaction.date}</Text>
              </View>
            </View>
            <View style={styles.transactionRight}>
              <Text
                style={[
                  styles.transactionAmount,
                  transaction.isIncome ? styles.incomeAmount : styles.expenseAmount,
                ]}
              >
                {transaction.isIncome ? '+' : '-'}₦{transaction.amount.toLocaleString()}
              </Text>
              <View
                style={[
                  styles.statusBadge,
                  transaction.status === 'posted' ? styles.postedBadge : styles.draftBadge,
                ]}
              >
                <Text style={styles.statusText}>{transaction.status}</Text>
              </View>
            </View>
          </TouchableOpacity>
        ))}
      </View>

      {/* Voucher Summary */}
      <View style={styles.summarySection}>
        <Text style={styles.sectionTitle}>Voucher Summary (This Month)</Text>
        <View style={styles.summaryGrid}>
          <View style={styles.summaryItem}>
            <Icon name="receipt-text" size={20} color="#2b6399" />
            <Text style={styles.summaryLabel}>Sales</Text>
            <Text style={styles.summaryValue}>{data.voucherSummary.sales}</Text>
          </View>
          <View style={styles.summaryItem}>
            <Icon name="cart" size={20} color="#8b5cf6" />
            <Text style={styles.summaryLabel}>Purchase</Text>
            <Text style={styles.summaryValue}>{data.voucherSummary.purchase}</Text>
          </View>
          <View style={styles.summaryItem}>
            <Icon name="cash-minus" size={20} color="#ef4444" />
            <Text style={styles.summaryLabel}>Payment</Text>
            <Text style={styles.summaryValue}>{data.voucherSummary.payment}</Text>
          </View>
          <View style={styles.summaryItem}>
            <Icon name="cash-plus" size={20} color="#10b981" />
            <Text style={styles.summaryLabel}>Receipt</Text>
            <Text style={styles.summaryValue}>{data.voucherSummary.receipt}</Text>
          </View>
        </View>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f3f4f6',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#fff',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1f2937',
  },
  filterButton: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    backgroundColor: '#eff6ff',
    borderRadius: 8,
  },
  filterText: {
    marginLeft: 4,
    color: '#2b6399',
    fontWeight: '600',
  },
  metricsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    padding: 12,
    gap: 12,
  },
  metricCard: {
    flex: 1,
    minWidth: '46%',
    backgroundColor: '#fff',
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  revenueCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#10b981',
  },
  expenseCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#ef4444',
  },
  profitCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#8b5cf6',
  },
  outstandingCard: {
    borderLeftWidth: 4,
    borderLeftColor: '#f59e0b',
  },
  metricHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  metricLabel: {
    marginLeft: 8,
    fontSize: 14,
    color: '#6b7280',
    fontWeight: '500',
  },
  metricValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 4,
  },
  metricChange: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  metricChangeText: {
    marginLeft: 4,
    fontSize: 12,
    color: '#6b7280',
  },
  metricSubtext: {
    fontSize: 12,
    color: '#6b7280',
    marginTop: 4,
  },
  chartSection: {
    backgroundColor: '#fff',
    margin: 12,
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  chartHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1f2937',
  },
  periodSelector: {
    flexDirection: 'row',
    gap: 8,
  },
  periodButton: {
    paddingHorizontal: 16,
    paddingVertical: 6,
    backgroundColor: '#f3f4f6',
    borderRadius: 6,
  },
  periodButtonActive: {
    backgroundColor: '#2b6399',
  },
  periodText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#6b7280',
  },
  periodTextActive: {
    color: '#fff',
  },
  chart: {
    marginVertical: 8,
    borderRadius: 16,
  },
  quickActionsSection: {
    backgroundColor: '#fff',
    margin: 12,
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  quickActionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
    marginTop: 12,
  },
  quickActionButton: {
    flex: 1,
    minWidth: '22%',
    aspectRatio: 1,
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  invoiceButton: {
    backgroundColor: '#2b6399',
  },
  paymentButton: {
    backgroundColor: '#ef4444',
  },
  receiptButton: {
    backgroundColor: '#10b981',
  },
  journalButton: {
    backgroundColor: '#8b5cf6',
  },
  quickActionText: {
    marginTop: 8,
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  moreActionsButton: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 12,
    padding: 12,
    backgroundColor: '#eff6ff',
    borderRadius: 8,
  },
  moreActionsText: {
    color: '#2b6399',
    fontWeight: '600',
    marginRight: 4,
  },
  recentSection: {
    backgroundColor: '#fff',
    margin: 12,
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  viewAllText: {
    color: '#2b6399',
    fontWeight: '600',
    fontSize: 14,
  },
  transactionItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#f3f4f6',
  },
  transactionLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  transactionInfo: {
    marginLeft: 12,
    flex: 1,
  },
  transactionType: {
    fontSize: 14,
    fontWeight: '600',
    color: '#1f2937',
  },
  transactionNumber: {
    fontSize: 12,
    color: '#6b7280',
    marginTop: 2,
  },
  transactionDate: {
    fontSize: 11,
    color: '#9ca3af',
    marginTop: 2,
  },
  transactionRight: {
    alignItems: 'flex-end',
  },
  transactionAmount: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  incomeAmount: {
    color: '#10b981',
  },
  expenseAmount: {
    color: '#ef4444',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
  },
  postedBadge: {
    backgroundColor: '#d1fae5',
  },
  draftBadge: {
    backgroundColor: '#fef3c7',
  },
  statusText: {
    fontSize: 10,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  summarySection: {
    backgroundColor: '#fff',
    margin: 12,
    marginBottom: 24,
    padding: 16,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  summaryGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
    marginTop: 12,
  },
  summaryItem: {
    flex: 1,
    minWidth: '22%',
    alignItems: 'center',
    padding: 12,
    backgroundColor: '#f9fafb',
    borderRadius: 8,
  },
  summaryLabel: {
    marginTop: 4,
    fontSize: 11,
    color: '#6b7280',
  },
  summaryValue: {
    marginTop: 4,
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1f2937',
  },
});
```

---

### Key Features

1. **Financial Metrics Cards**
   - Revenue with % change
   - Expenses with % change
   - Profit with % change
   - Outstanding invoices count

2. **Interactive Line Chart**
   - Revenue vs Expenses over time
   - Toggle between 6 months and 1 year view
   - Smooth bezier curves

3. **Quick Actions**
   - Create Invoice, Payment, Receipt, Journal Entry
   - "More Actions" button for additional voucher types

4. **Recent Transactions List**
   - Last 10 transactions
   - Color-coded (green for income, red for expense)
   - Status badges (posted/draft)
   - Tap to view details

5. **Voucher Summary**
   - Monthly count by voucher type
   - Visual representation with icons

---

## 2. Account Groups Management

Coming in next batch...

