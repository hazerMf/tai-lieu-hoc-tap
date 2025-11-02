import socket
import hashlib
from sys import argv, exit
from packet import *


# Check and parse command-line arguments
if len(argv) != 4:
    print("Not enough or too many arguments")
    print("Usage: python tcpserver.py <server port> <password> <input file>")
    exit(1)

# Address and host
addr = '127.0.0.1'
try:
    port = int(argv[1])
except ValueError:
    print("Error: Server port must be a number.")
    exit(1)

passwd = argv[2]

fileIn = argv[3]

password_attempts = 0

quitFlag = True

# Create the server socket
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind((addr, port))

# listen and connect to the client
print("Waiting..")
server_socket.listen()
conn, address = server_socket.accept()
print("Connection from: " + str(address))

while quitFlag:

    # Receiving a packet from client and processing it
    data = conn.recv(1024)
    recv_pkt = decode_packet(data)
    print("Receive message: " + reserve_mapping(recv_pkt.header))

    # Check the header
    if recv_pkt.header == JOIN_REQ:
        # Receive JOIN_REQ packet from client.
        # Send PASS_REQ to client
        tran_pkt = ctrl_msg_packet(PASS_REQ,0)
        conn.send(tran_pkt.encode())
        # Complete this part

    elif recv_pkt.header == PASS_RESP:
        # Receive PASS_RESP_PACKET from client.
        if password_attempts < 2:
            if recv_pkt.password == passwd:
                # the password is correct
                # Send a ACCEPT packet
                # Complete this part
                tran_pkt = ctrl_msg_packet(PASS_ACCEPT,0)
                conn.send(tran_pkt.encode())
                # Read data from txt file (test.txt) and send it
                # Complete this part
                file_path = "data.txt"
                try:
                    with open(file_path, 'r', encoding='utf-8') as file:
                        content = file.read()
                        data_pkt = data_packet(DATA, 5, 1024, content)
                        conn.send(data_pkt.encode())
                        
                except FileNotFoundError:
                    print(f"Error: The file '{file_path}' was not found.")
                except Exception as e:
                    print(f"An error occurred: {e}")
                # Calculate SHA1-Digest of the data
                # Complete this part
                

                data_sha = hashlib.sha1(content.encode())
                # Send the TERMINATE packet
                # Complete this part
                ter_pkt = terminate_packet(TERMINATE,6,data_sha.digest())
                conn.send(ter_pkt.encode())

                print("Download Completed Sucessfully!")

                quitFlag = False

            else:
                # The password is not correct.
                # Send another PASS_REQ
                tran_pkt = ctrl_msg_packet(PASS_REQ,2)
                conn.send(tran_pkt.encode())
                # Complete this part
                
                password_attempts += 1

        else:
            # Send a REJECT to client after 3 wrong attempts
            tran_pkt = ctrl_msg_packet(REJECT,7)
            conn.send(tran_pkt.encode())
            # Complete this part
            
            print("Wrong password 3 times!")
            print("REJECT!")
            quitFlag = False

    else:
        quitFlag = False

# Close the connection
server_socket.close()
