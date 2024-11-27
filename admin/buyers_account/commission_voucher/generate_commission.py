import sys
from time import gmtime, strftime
import psycopg2  # Assuming you are using psycopg2 for PostgreSQL in Python

def gen_comm():
    try:
        # Connect to the database
        conn = psycopg2.connect("dbname=CAR_TESTDB user=postgres password=admin12345")
        cursor = conn.cursor()
    except psycopg2.Error as e:
        print(f"Database connection error: {e}")
        return

    l_date1 = gmtime()  # Adjust this according to your logic
    l_print_date = "2024-09-09"
    l_count = 0
    l_sql = """
    SELECT t_commission.c_code, t_commission.c_amount, t_commission.c_account_no, t_commission.c_rate,
           t_buyers_account.c_net_tcp, t_buyers_account.c_down_percent, t_buyers_account.c_b1_first_name,
           t_buyers_account.c_b1_last_name, t_commission.c_sale, t_buyers_account.ra , t_buyers_account.cts, t_buyers_account.das
    FROM t_agents
    LEFT JOIN t_commission ON t_agents.c_code = t_commission.c_code
    LEFT JOIN t_buyers_account ON t_commission.c_account_no = t_buyers_account.c_account_no
    WHERE t_commission.c_date_of_sale >= '2024-01-01'
    ORDER BY t_commission.c_date_of_sale
    """
    
    try:
        cursor.execute(l_sql)
        l_rslt = cursor.fetchall()
        l_rows = len(l_rslt)
    except psycopg2.Error as e:
        print(f"Query execution error: {e}")
        cursor.close()
        conn.close()
        return

    for x in range(l_rows):
        try:
            l_code = str(l_rslt[x][0])
            l_amount = l_rslt[x][1]
            l_acc_no = str(l_rslt[x][2])
           
            # Get project acronym
            project_query = "SELECT c_acronym FROM t_projects WHERE c_code = %s"
            cursor.execute(project_query, (l_acc_no[0:3],))
            l_acro = cursor.fetchone()
            
            if not l_acro:
                print("Code No Project Found!")
                continue

            l_blk = int(l_acc_no[3:6])
            l_lot = int(l_acc_no[6:8])
            l_location = f"{l_acro[0]} {l_blk} {l_lot}"
            l_rate = l_rslt[x][3]
            l_net_tcp = l_rslt[x][4]
            l_down_per = l_rslt[x][5]
            l_first_name = l_rslt[x][6]
            l_last_name = l_rslt[x][7]
            l_comm_type = l_rslt[x][8]
            l_buyer_name = f"{l_last_name}, {l_first_name}"

            # Get commission values
            get_val = due_commission(conn, l_acc_no, l_code, l_net_tcp, l_down_per, l_amount, l_comm_type)
            l_due_comm, l_comm_amt, l_tot_dp, l_prev_comm, l_prev_amt, l_comm_count = get_val[0]

            l_comm_count += 1
           
            insert_sql = """
            INSERT INTO t_new_commission_log (c_code, c_account_no, c_buyers_name, c_commission_amount, c_rate,
                                              c_net_commission, c_prev_comm, c_due_comm, c_print_date, c_prev_comm_amt,
                                              c_commission_count)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            values = (l_code, l_acc_no, l_buyer_name, l_comm_amt, l_rate, l_amount, l_prev_comm, l_due_comm, l_print_date, l_prev_amt, l_comm_count)
           
            if l_due_comm > l_prev_comm:
                cursor.execute(insert_sql, values)
                conn.commit()
                l_count += 1
                #print("Commission Updated!!")
        except psycopg2.Error as e:
            print(f"Error processing row {x}: {e}")


    print(str(l_count) + " Account Added to New Commission!!!")    
    cursor.close()
    conn.close()

def due_commission(conn, l_acc_no, l_code, l_net_tcp, l_down_per, l_amount, l_comm_type):
    cursor = conn.cursor()
    l_sql1 = "SELECT SUM(c_principal) + SUM(c_rebate) FROM t_payment WHERE c_account_no = %s"
    cursor.execute(l_sql1, (l_acc_no,))
    l_prin = cursor.fetchone()[0]
   # print(str(l_prin) + " Principal")
    l_tot_dp = l_net_tcp * (l_down_per / 100)
    if l_tot_dp == 0.0:
        l_val = 80.0
        l_get_comm = l_amount * (l_val / 100)
    else:
        if l_prin >= l_tot_dp:
            l_val = 80.0
        else:
            l_val = (l_prin / l_tot_dp) * 80
        
        l_get_comm = l_amount * (l_val / 100)
    
    if l_prin >= l_net_tcp:
        l_val = 100.0
        l_get_comm = l_amount * (l_val / 100)
    
    if l_comm_type == 7:
        l_val = 100.0
        l_get_comm = l_amount * (l_val / 100)
    
    if 20 <= l_val <= 39.99:
        l_val = 20
    elif 40 <= l_val <= 59.99:
        l_val = 40
    elif 60 <= l_val <= 79.99:
        l_val = 60
    elif 80 <= l_val <= 99.99:
        l_val = 80
    elif l_val >= 100:
        l_val = 100
    else:
        l_val = 0
    
    l_sql2 = """
    SELECT c_due_comm, c_commission_amount, c_prev_comm_amt, c_commission_count
    FROM t_new_commission_log
    WHERE c_account_no = %s AND c_code = %s
    ORDER BY c_commission_count DESC
    LIMIT 1
    """
    cursor.execute(l_sql2, (l_acc_no, l_code))
    result = cursor.fetchone()
    
    if result:
        l_prev_comm, l_prev_amt, l_comm_count = result[0], result[1], result[3]
    else:
        l_prev_comm, l_prev_amt, l_comm_count = 0.0, 0.0, 0
    
    l_data = (l_val, l_get_comm, l_tot_dp, l_prev_comm, l_prev_amt, l_comm_count)
    cursor.close()
    return [l_data]

if __name__ == "__main__":
    #print("Script is running directly")
    gen_comm()
