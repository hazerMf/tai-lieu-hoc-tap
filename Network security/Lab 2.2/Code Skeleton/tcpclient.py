import socket
import hashlib
from sys import argv, exit
from packet import *


# Check and parse command-line arguments
if len(argv) != 7:
    print("Not enough or too many arguments")
    print("Usage: python tcpclient.py <server IP address> <server port> <clientpwd1> <clientpwd2> <clientpwd3> <output file> ")
    exit(1)

server_addr = argv[1]
# The port must be an integer
try:
    server_port = int(argv[2])
except ValueError:
    print("Error: Server port must be a number.")
    exit(1)

passwd1 = argv[3]

passwd2 = argv[4]

passwd3 = argv[5]

fileOut = argv[6]

password_attempts = 0

quitFlag = True

# Create the client socket and connect to the server socket
try:
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
except socket.error as e:
    print(f"Failed to create socket: {e}")
    exit(1)

client_socket.connect((server_addr, server_port))
print(f"Contacting server at {server_addr}:{server_port}...")

# Send JOIN_REQ packet
tran_pkt = ctrl_msg_packet(JOIN_REQ, 1)
client_socket.send(tran_pkt.encode())


t = 1
while (quitFlag):

    # Receiving a packet from server and processing it
    data = client_socket.recv(1024)
    recv_pkt = decode_packet(data)

    print("Receive message: " + reserve_mapping(recv_pkt.header))

    # Check the header of the received packet
    
    if (recv_pkt.header == PASS_REQ):
        # Receive PASS_REQ packet from client.
        # Send PASS_RESP to client
        
        if(t==1):
            tran_pkt = pass_resp_packet(PASS_RESP,3,passwd1)
            client_socket.send(tran_pkt.encode())
            t+=1
        elif(t==2):
            tran_pkt = pass_resp_packet(PASS_RESP,3,passwd2)
            client_socket.send(tran_pkt.encode())
            t+=1
        else:
            tran_pkt = pass_resp_packet(PASS_RESP,3,passwd3)
            client_socket.send(tran_pkt.encode())
        # Complete this part

    elif (recv_pkt.header == PASS_ACCEPT):
        # Receive PASS_ACCEPT packet from server.
        # Do nothing
        pass

    elif (recv_pkt.header == DATA):
        #  Receive DATA_PACKET from server.
        # Write data to the fileOut file

        with open(fileOut, "w") as file:
            file.write(recv_pkt.data)
        # Complete this part

    elif (recv_pkt.header == TERMINATE):
        # Receive TERMINATE packet from server.
        # Calculate SHA1-Digest from the received data
        # Compare it with the received digest
        
        file_path = "recv_data.txt"
        try:
            with open(file_path, 'r', encoding='utf-8') as file:
                content = file.read()
                self_digest = hashlib.sha1(content.encode())
        except FileNotFoundError:
            print(f"Error: The file '{file_path}' was not found.")
        except Exception as e:
            print(f"An error occurred: {e}")

        self_digest = hashlib.sha1(content.encode())

        if (recv_pkt.digest == self_digest.digest()):
            print("DATA INTEGRITY CONFIRMED!")
        else:
            print("DATA HAS BEEN ALTERED!")
        print("Download Completed Succesfully!")
        quitFlag = False

    elif (recv_pkt.header == REJECT):
        # Close the session
        print("Wrong password 3 times!")
        print("ABORT!")
        quitFlag = False

    else:
        quitFlag = False

# Close the connection
client_socket.close()
